<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResidentBlotterRequest;
use App\Http\Requests\StoreResidentCertificateRequest;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResidentServiceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['status' => 'nullable|in:Pending,Completed,Declined']);
        $status = $filters['status'] ?? null;
        $query = ServiceRequest::query()->where('resident_id', $request->user()->resident_id);

        if ($status !== null) {
            $query->where('status', $status);
        }

        $requests = $query->latest('updated_at')->latest('id')->paginate(10)->withQueryString();

        return view('service-requests.resident.index', compact('requests', 'status'));
    }

    public function createCertificate(): View
    {
        return view('service-requests.resident.create-certificate');
    }

    public function storeCertificate(StoreResidentCertificateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $serviceRequest = ServiceRequest::create([
            'resident_id' => $request->user()->resident_id,
            'type' => ServiceRequest::TYPE_CERTIFICATE,
            'certificate_type' => $validated['certificate_type'],
            'purpose' => $validated['purpose'],
            'status' => ServiceRequest::STATUS_PENDING,
        ]);

        return redirect()->route('account.requests.show', $serviceRequest)
            ->with('success', 'Your document request has been submitted for staff review.');
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

        return view('service-requests.resident.show', compact('serviceRequest'));
    }
}
