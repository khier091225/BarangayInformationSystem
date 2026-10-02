<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecordCashPaymentRequest;
use App\Models\Certificate;
use App\Models\Payment;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class DemoPaymentController extends Controller
{
    public function store(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        abort_unless($serviceRequest->resident_id === $request->user()->resident_id, 404);
        abort_unless(
            $serviceRequest->type === ServiceRequest::TYPE_CERTIFICATE
            && $serviceRequest->status === ServiceRequest::STATUS_AWAITING_PAYMENT
            && (float) $serviceRequest->fee_amount > 0,
            422,
        );

        $payment = DB::transaction(function () use ($serviceRequest): Payment {
            $lockedRequest = ServiceRequest::query()->lockForUpdate()->findOrFail($serviceRequest->id);
            abort_unless(
                $lockedRequest->status === ServiceRequest::STATUS_AWAITING_PAYMENT
                && (float) $lockedRequest->fee_amount > 0,
                422,
            );
            $pendingPayment = $lockedRequest->payments()
                ->where('status', Payment::STATUS_PENDING)
                ->latest('id')
                ->first();

            if ($pendingPayment?->provider === Payment::PROVIDER_DEMO_QRPH
                && $pendingPayment->expires_at?->isFuture()) {
                return $pendingPayment;
            }

            if ($pendingPayment !== null) {
                $pendingPayment->update(['status' => Payment::STATUS_CANCELLED]);
            }

            return $lockedRequest->payments()->create([
                'provider' => Payment::PROVIDER_DEMO_QRPH,
                'amount' => $lockedRequest->fee_amount,
                'status' => Payment::STATUS_PENDING,
                'expires_at' => now()->addMinutes(config('demo_payments.expires_minutes')),
            ]);
        });

        return redirect()->route('account.requests.show', $serviceRequest)
            ->with('success', $payment->wasRecentlyCreated
                ? 'QRPH code generated. Scan it with your phone to continue.'
                : 'Your active QRPH code is ready to scan.');
    }

    public function storeCash(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        abort_unless($serviceRequest->resident_id === $request->user()->resident_id, 404);
        abort_unless(
            $serviceRequest->type === ServiceRequest::TYPE_CERTIFICATE
            && $serviceRequest->status === ServiceRequest::STATUS_AWAITING_PAYMENT
            && (float) $serviceRequest->fee_amount > 0,
            422,
        );

        $payment = DB::transaction(function () use ($serviceRequest): Payment {
            $lockedRequest = ServiceRequest::query()->lockForUpdate()->findOrFail($serviceRequest->id);
            abort_unless(
                $lockedRequest->status === ServiceRequest::STATUS_AWAITING_PAYMENT
                && (float) $lockedRequest->fee_amount > 0,
                422,
            );

            $pendingPayment = $lockedRequest->payments()
                ->where('status', Payment::STATUS_PENDING)
                ->latest('id')
                ->first();

            if ($pendingPayment?->provider === Payment::PROVIDER_CASH) {
                return $pendingPayment;
            }

            if ($pendingPayment !== null) {
                $pendingPayment->update(['status' => Payment::STATUS_CANCELLED]);
            }

            return $lockedRequest->payments()->create([
                'provider' => Payment::PROVIDER_CASH,
                'amount' => $lockedRequest->fee_amount,
                'status' => Payment::STATUS_PENDING,
                'expires_at' => null,
            ]);
        });

        return redirect()->route('account.requests.show', $serviceRequest)
            ->with('success', $payment->wasRecentlyCreated
                ? 'Cash payment selected. Pay at the barangay hall and present your request number.'
                : 'Your request is already set for cash payment.');
    }

    public function show(Payment $payment): View
    {
        abort_unless($payment->provider === Payment::PROVIDER_DEMO_QRPH && $payment->expires_at !== null, 404);
        $this->expireIfNeeded($payment);
        $payment->load('serviceRequest');

        $confirmUrl = URL::temporarySignedRoute(
            'demo-payments.confirm',
            $payment->expires_at,
            ['payment' => $payment],
            absolute: false,
        );

        return view('demo-payments.show', compact('payment', 'confirmUrl'));
    }

    public function confirm(Payment $payment): RedirectResponse
    {
        abort_unless($payment->provider === Payment::PROVIDER_DEMO_QRPH, 404);

        $result = DB::transaction(function () use ($payment): string {
            $lockedPayment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            $serviceRequest = ServiceRequest::query()->lockForUpdate()->findOrFail($lockedPayment->service_request_id);

            if ($lockedPayment->status === Payment::STATUS_PAID) {
                return 'paid';
            }

            if ($lockedPayment->status !== Payment::STATUS_PENDING || $lockedPayment->expires_at->isPast()) {
                $lockedPayment->update(['status' => Payment::STATUS_EXPIRED]);

                return 'expired';
            }

            $this->issueCertificate($serviceRequest, $lockedPayment);

            $lockedPayment->update([
                'status' => Payment::STATUS_PAID,
                'paid_at' => now(),
            ]);

            return 'paid';
        });

        return redirect($this->signedPaymentPath($payment->fresh()))
            ->with($result === 'paid' ? 'success' : 'warning', $result === 'paid'
                ? 'Payment completed. BIS has issued the certificate.'
                : 'This QRPH session has expired. Generate a new code from the BIS request page.');
    }

    public function confirmCash(RecordCashPaymentRequest $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validated();
        $completed = DB::transaction(function () use ($request, $payment, $validated): bool {
            $lockedPayment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            $serviceRequest = ServiceRequest::query()->lockForUpdate()->findOrFail($lockedPayment->service_request_id);

            if ($lockedPayment->status === Payment::STATUS_PAID) {
                return false;
            }

            abort_unless(
                $lockedPayment->provider === Payment::PROVIDER_CASH
                && $lockedPayment->status === Payment::STATUS_PENDING
                && $serviceRequest->status === ServiceRequest::STATUS_AWAITING_PAYMENT,
                422,
            );

            $this->issueCertificate($serviceRequest, $lockedPayment);
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

    public function status(Request $request, Payment $payment): JsonResponse
    {
        $payment->load('serviceRequest');
        abort_unless($payment->serviceRequest->resident_id === $request->user()->resident_id, 404);
        $this->expireIfNeeded($payment);

        return response()->json([
            'status' => $payment->status,
            'redirect_url' => $payment->status === Payment::STATUS_PAID
                ? route('account.requests.show', $payment->serviceRequest, false)
                : null,
            'expires_at' => $payment->expires_at?->toIso8601String(),
        ]);
    }

    private function expireIfNeeded(Payment $payment): void
    {
        if ($payment->provider === Payment::PROVIDER_DEMO_QRPH
            && $payment->status === Payment::STATUS_PENDING
            && $payment->expires_at?->isPast()) {
            $payment->update(['status' => Payment::STATUS_EXPIRED]);
        }
    }

    private function issueCertificate(ServiceRequest $serviceRequest, Payment $payment): void
    {
        if ($serviceRequest->certificate_id !== null) {
            return;
        }

        $certificate = Certificate::create([
            'resident_id' => $serviceRequest->resident_id,
            'certificate_type' => $serviceRequest->certificate_type,
            'purpose' => $serviceRequest->purpose,
            'fee' => $payment->amount,
            'date_issued' => today(),
        ]);

        $serviceRequest->update([
            'status' => ServiceRequest::STATUS_COMPLETED,
            'certificate_id' => $certificate->id,
        ]);
    }

    private function signedPaymentPath(Payment $payment): string
    {
        return URL::temporarySignedRoute(
            'demo-payments.show',
            $payment->expires_at,
            ['payment' => $payment],
            absolute: false,
        );
    }
}
