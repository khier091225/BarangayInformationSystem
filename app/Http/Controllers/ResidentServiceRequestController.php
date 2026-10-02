<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResidentBlotterRequest;
use App\Http\Requests\StoreResidentCertificateRequest;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Support\CertificateFees;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class ResidentServiceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => 'nullable|in:Pending,Awaiting Payment,Completed,Declined',
            'type' => 'nullable|in:certificate,blotter',
        ]);
        $status = $filters['status'] ?? null;
        $type = $filters['type'] ?? null;
        $requestLabel = match ($type) {
            ServiceRequest::TYPE_CERTIFICATE => 'document requests',
            ServiceRequest::TYPE_BLOTTER => 'blotter requests',
            default => 'requests',
        };
        $query = ServiceRequest::query()->where('resident_id', $request->user()->resident_id);

        if ($type !== null) {
            $query->where('type', $type);
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        $requests = $query->latest('updated_at')->latest('id')->paginate(10)->withQueryString();

        return view('service-requests.resident.index', compact('requests', 'status', 'type', 'requestLabel'));
    }

    public function createCertificate(): View
    {
        $certificateFees = CertificateFees::rates();

        return view('service-requests.resident.create-certificate', compact('certificateFees'));
    }

    public function storeCertificate(StoreResidentCertificateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $serviceRequest = ServiceRequest::create([
            'resident_id' => $request->user()->resident_id,
            'type' => ServiceRequest::TYPE_CERTIFICATE,
            'certificate_type' => $validated['certificate_type'],
            'purpose' => $validated['purpose'],
            'fee_amount' => CertificateFees::amountFor($validated['certificate_type']),
            'status' => ServiceRequest::STATUS_PENDING,
        ]);

        return redirect()->route('account.requests.show', $serviceRequest)
            ->with('success', 'Your document request has been submitted for verification.');
    }

    public function createBlotter(): View
    {
        return view('service-requests.resident.create-blotter');
    }

    public function storeBlotter(StoreResidentBlotterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $serviceRequest = ServiceRequest::create([
            'resident_id' => $request->user()->resident_id,
            'type' => ServiceRequest::TYPE_BLOTTER,
            'respondent' => trim($validated['respondent'] ?? '') ?: 'Unknown',
            'incident' => $validated['incident'],
            'incident_date' => $validated['incident_date'] ?? today('Asia/Manila')->toDateString(),
            'status' => ServiceRequest::STATUS_PENDING,
        ]);

        return redirect()->route('account.requests.show', $serviceRequest)
            ->with('success', 'Your blotter report has been submitted for staff review.');
    }

    public function show(Request $request, ServiceRequest $serviceRequest): View
    {
        abort_unless($serviceRequest->resident_id === $request->user()->resident_id, 404);
        $serviceRequest->load(['blotter.updates', 'latestPayment']);
        $payment = $serviceRequest->latestPayment;
        $demoPaymentUrl = null;

        if ($payment?->provider === Payment::PROVIDER_DEMO_QRPH
            && $payment->status === Payment::STATUS_PENDING
            && $payment->expires_at?->isPast()) {
            $payment->update(['status' => Payment::STATUS_EXPIRED]);
        }

        if ($payment?->provider === Payment::PROVIDER_DEMO_QRPH
            && $payment->status === Payment::STATUS_PENDING
            && $payment->expires_at !== null) {
            $relativePath = URL::temporarySignedRoute(
                'demo-payments.show',
                $payment->expires_at,
                ['payment' => $payment],
                absolute: false,
            );
            $publicBaseUrl = rtrim((string) config('demo_payments.public_url'), '/');
            $demoPaymentUrl = ($publicBaseUrl !== '' ? $publicBaseUrl : $request->getSchemeAndHttpHost())
                .'/'.ltrim($relativePath, '/');
        }

        return view('service-requests.resident.show', compact('serviceRequest', 'demoPaymentUrl'));
    }
}
