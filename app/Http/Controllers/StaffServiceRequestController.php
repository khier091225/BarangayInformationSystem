<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewServiceRequest;
use App\Models\Blotter;
use App\Models\Certificate;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StaffServiceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => 'nullable|in:Pending,Completed,Declined',
        ]);
        $status = $filters['status'] ?? ServiceRequest::STATUS_PENDING;

        $requests = ServiceRequest::query()
            ->with('resident')
            ->where('status', $status)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('service-requests.staff.index', compact('requests', 'status'));
    }

    public function show(ServiceRequest $serviceRequest): View
    {
        $serviceRequest->load(['resident', 'reviewer', 'certificate', 'blotter']);

        return view('service-requests.staff.show', compact('serviceRequest'));
    }

    public function review(ReviewServiceRequest $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $validated = $request->validated();
        $updated = DB::transaction(function () use ($request, $serviceRequest, $validated): bool {
            $lockedRequest = ServiceRequest::query()
                ->with('resident')
                ->lockForUpdate()
                ->findOrFail($serviceRequest->id);

            if ($lockedRequest->status !== ServiceRequest::STATUS_PENDING) {
                return false;
            }

            $result = [
                'status' => $validated['decision'] === 'complete'
                    ? ServiceRequest::STATUS_COMPLETED
                    : ServiceRequest::STATUS_DECLINED,
                'response_note' => $validated['response_note'] ?? null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ];

            if ($validated['decision'] === 'complete' && $lockedRequest->type === ServiceRequest::TYPE_CERTIFICATE) {
                $certificate = Certificate::create([
                    'resident_id' => $lockedRequest->resident_id,
                    'certificate_type' => $lockedRequest->certificate_type,
                    'purpose' => $lockedRequest->purpose,
                    'date_issued' => today(),
                ]);
                $result['certificate_id'] = $certificate->id;
            } elseif ($validated['decision'] === 'complete' && $lockedRequest->type === ServiceRequest::TYPE_BLOTTER) {
                $blotter = Blotter::create([
                    'complainant' => $lockedRequest->resident->full_name,
                    'respondent' => $lockedRequest->respondent,
                    'incident' => $lockedRequest->incident,
                    'incident_date' => $lockedRequest->incident_date,
                    'status' => 'Pending',
                ]);
                $result['blotter_id'] = $blotter->id;
            }

            $lockedRequest->update($result);

            return true;
        });

        if (! $updated) {
            return redirect()->route('service-requests.show', $serviceRequest)
                ->with('warning', 'This request has already been reviewed.');
        }

        return redirect()->route('service-requests.show', $serviceRequest)
            ->with('success', $validated['decision'] === 'complete'
                ? 'Request completed and the official record was created.'
                : 'Request declined. The resident can see your response.');
    }
}
