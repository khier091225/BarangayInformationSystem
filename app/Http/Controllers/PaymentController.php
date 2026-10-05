<?php

namespace App\Http\Controllers;

use App\Http\PayMongoPayments;
use App\Http\Requests\RecordCashPaymentRequest;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Support\CertificateIssuance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function __construct(public PayMongoPayments $payments, public CertificateIssuance $certificates) {}

    public function store(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorizeRequest($request, $serviceRequest);
        $payment = $this->payments->start($serviceRequest);

        return redirect()->away($payment->checkout_url);
    }

    public function storeCash(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorizeRequest($request, $serviceRequest);
        $onlinePayment = $serviceRequest->payments()->where('provider', Payment::PROVIDER_PAYMONGO_QRPH)
            ->where('status', Payment::STATUS_PENDING)->latest('id')->first();
        if ($onlinePayment !== null) {
            $this->payments->close($onlinePayment);
            if ($onlinePayment->status === Payment::STATUS_PAID) {
                return redirect()->route('account.requests.show', $serviceRequest)
                    ->with('success', 'Your online payment was confirmed and the certificate was issued.');
            }
        }

        $payment = DB::transaction(function () use ($serviceRequest): Payment {
            $lockedRequest = ServiceRequest::query()->lockForUpdate()->findOrFail($serviceRequest->id);
            abort_unless($lockedRequest->status === ServiceRequest::STATUS_AWAITING_PAYMENT, 422);
            $pending = $lockedRequest->payments()->where('status', Payment::STATUS_PENDING)->latest('id')->first();
            if ($pending?->provider === Payment::PROVIDER_CASH) {
                return $pending;
            }

            if ($pending?->provider === Payment::PROVIDER_PAYMONGO_QRPH) {
                throw ValidationException::withMessages(['payment' => 'An online checkout is still active. Please refresh this page before switching to cash.']);
            }

            $pending?->update(['status' => Payment::STATUS_CANCELLED]);

            return $lockedRequest->payments()->create([
                'provider' => Payment::PROVIDER_CASH,
                'amount' => $lockedRequest->fee_amount,
                'status' => Payment::STATUS_PENDING,
            ]);
        });

        return redirect()->route('account.requests.show', $serviceRequest)
            ->with('success', $payment->wasRecentlyCreated
                ? 'Cash payment selected. Pay at the barangay hall and present your request number.'
                : 'Your request is already set for cash payment.');
    }

    public function returnFromCheckout(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorizePayment($request, $payment);
        try {
            $this->payments->synchronize($payment, force: true);
        } catch (ValidationException) {
            return redirect()->route('account.requests.show', $payment->serviceRequest)
                ->with('warning', 'We are still waiting for payment confirmation. Please keep this page open; it will update automatically.');
        }

        return redirect()->route('account.requests.show', $payment->serviceRequest)
            ->with($payment->status === Payment::STATUS_PAID ? 'success' : 'warning',
                $payment->status === Payment::STATUS_PAID
                    ? 'Your payment was confirmed and the certificate was issued.'
                    : 'We are waiting for payment confirmation. This page will update automatically.');
    }

    public function status(Request $request, Payment $payment): JsonResponse
    {
        $this->authorizePayment($request, $payment);
        $checkingAvailable = true;
        try {
            $this->payments->synchronize($payment);
        } catch (ValidationException) {
            $checkingAvailable = false;
        }
        $payment->refresh();

        return response()->json([
            'status' => $payment->status,
            'checking_available' => $checkingAvailable,
            'redirect_url' => $payment->status === Payment::STATUS_PAID
                ? route('account.requests.show', $payment->serviceRequest, false)
                : null,
            'expires_at' => $payment->expires_at?->toIso8601String(),
        ]);
    }

    public function confirmCash(RecordCashPaymentRequest $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->provider === Payment::PROVIDER_CASH, 404);
        $validated = $request->validated();
        $completed = DB::transaction(function () use ($request, $payment, $validated): bool {
            $serviceRequest = ServiceRequest::query()->lockForUpdate()->findOrFail($payment->service_request_id);
            $lockedPayment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            if ($lockedPayment->status === Payment::STATUS_PAID) {
                return false;
            }

            abort_unless($lockedPayment->status === Payment::STATUS_PENDING
                && $serviceRequest->status === ServiceRequest::STATUS_AWAITING_PAYMENT, 422);

            $this->certificates->issue($serviceRequest, $lockedPayment);
            $lockedPayment->update([
                'status' => Payment::STATUS_PAID,
                'paid_at' => now(),
                'receipt_number' => $validated['receipt_number'],
                'payment_note' => $validated['payment_note'] ?? null,
                'recorded_by' => $request->user()->id,
            ]);

            return true;
        });

        return redirect()->route('service-requests.show', $payment->service_request_id)
            ->with($completed ? 'success' : 'warning', $completed
                ? 'Cash payment recorded and the certificate was issued.'
                : 'This cash payment has already been recorded.');
    }

    private function authorizeRequest(Request $request, ServiceRequest $serviceRequest): void
    {
        abort_unless($serviceRequest->resident_id === $request->user()->resident_id, 404);
        abort_unless($serviceRequest->type === ServiceRequest::TYPE_CERTIFICATE
            && $serviceRequest->status === ServiceRequest::STATUS_AWAITING_PAYMENT
            && (float) $serviceRequest->fee_amount > 0, 422);
    }

    private function authorizePayment(Request $request, Payment $payment): void
    {
        $payment->load('serviceRequest');
        abort_unless($payment->serviceRequest->resident_id === $request->user()->resident_id, 404);
    }
}
