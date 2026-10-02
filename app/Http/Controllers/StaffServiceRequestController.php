<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewServiceRequest;
use App\Models\Blotter;
use App\Models\Certificate;
use App\Models\ServiceRequest;
use App\Support\CertificateFees;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StaffServiceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => 'nullable|in:Pending,Awaiting Payment,Completed,Declined',
        ]);
        $status = $filters['status'] ?? ServiceRequest::STATUS_PENDING;

        $totals = ServiceRequest::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusCounts = [
            ServiceRequest::STATUS_PENDING => (int) ($totals[ServiceRequest::STATUS_PENDING] ?? 0),
            ServiceRequest::STATUS_AWAITING_PAYMENT => (int) ($totals[ServiceRequest::STATUS_AWAITING_PAYMENT] ?? 0),
            ServiceRequest::STATUS_COMPLETED => (int) ($totals[ServiceRequest::STATUS_COMPLETED] ?? 0),
            ServiceRequest::STATUS_DECLINED => (int) ($totals[ServiceRequest::STATUS_DECLINED] ?? 0),
        ];

        $requests = ServiceRequest::query()
            ->with('resident')
            ->where('status', $status)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('service-requests.staff.index', compact('requests', 'status', 'statusCounts'));
    }

    public function show(ServiceRequest $serviceRequest): View
    {
        $serviceRequest->load(['resident', 'reviewer', 'certificate', 'blotter', 'latestPayment.recorder']);
        $certificateFee = $serviceRequest->type === ServiceRequest::TYPE_CERTIFICATE
            ? CertificateFees::amountFor($serviceRequest->certificate_type)
            : null;

        return view('service-requests.staff.show', compact('serviceRequest', 'certificateFee'));
    }

    public function review(ReviewServiceRequest $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $validated = $request->validated();
        $outcome = DB::transaction(function () use ($request, $serviceRequest, $validated): string {
            $lockedRequest = ServiceRequest::query()
                ->with('resident')
                ->lockForUpdate()
                ->findOrFail($serviceRequest->id);

            if ($lockedRequest->status !== ServiceRequest::STATUS_PENDING) {
                return 'already-reviewed';
            }

            $result = [
                'status' => ServiceRequest::STATUS_DECLINED,
                'response_note' => $validated['response_note'] ?? null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ];

            if ($validated['decision'] === 'complete' && $lockedRequest->type === ServiceRequest::TYPE_CERTIFICATE) {
                $feeAmount = CertificateFees::amountFor($lockedRequest->certificate_type);
                $result['fee_amount'] = $feeAmount;

                if ((float) $feeAmount > 0) {
                    $result['status'] = ServiceRequest::STATUS_AWAITING_PAYMENT;
                } else {
                    $certificate = Certificate::create([
                        'resident_id' => $lockedRequest->resident_id,
                        'certificate_type' => $lockedRequest->certificate_type,
                        'purpose' => $lockedRequest->purpose,
                        'fee' => 0,
                        'date_issued' => today(),
                    ]);
                    $result['status'] = ServiceRequest::STATUS_COMPLETED;
                    $result['certificate_id'] = $certificate->id;
                }
            } elseif ($validated['decision'] === 'complete' && $lockedRequest->type === ServiceRequest::TYPE_BLOTTER) {
                $blotter = Blotter::create([
                    'complainant' => $lockedRequest->resident->full_name,
                    'respondent' => $lockedRequest->respondent,
                    'incident' => $lockedRequest->incident,
                    'incident_date' => $lockedRequest->incident_date,
                    'status' => 'Pending',
                ]);
                $result['status'] = ServiceRequest::STATUS_COMPLETED;
                $result['blotter_id'] = $blotter->id;
            }

            $lockedRequest->update($result);

            return $result['status'] === ServiceRequest::STATUS_AWAITING_PAYMENT
                ? 'awaiting-payment'
                : ($validated['decision'] === 'complete' ? 'completed' : 'declined');
        });

        if ($outcome === 'already-reviewed') {
            return redirect()->route('service-requests.show', $serviceRequest)
                ->with('warning', 'This request has already been reviewed.');
        }

        return redirect()->route('service-requests.show', $serviceRequest)
            ->with('success', match ($outcome) {
                'awaiting-payment' => 'Request approved. The resident can now generate the QRPH payment.',
                'completed' => 'Request completed and the official record was created.',
                default => 'Request declined. The resident can see your response.',
            });
    }
}
