<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyResidentRequest;
use App\Models\IncidentReport;
use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $recentRequests = collect();
        $latestReviewedRequest = null;
        $latestIncidentReport = null;
        $requestCounts = ['total' => 0, 'pending' => 0, 'awaiting_payment' => 0, 'completed' => 0, 'declined' => 0];
        $incidentCounts = ['total' => 0, 'active' => 0];

        if ($user->role === 'resident' && $user->resident_id !== null) {
            $latestIncidentReport = IncidentReport::query()->where('resident_id', $user->resident_id)
                ->latest('updated_at')->first();
            $incidentStatusTotals = IncidentReport::query()->where('resident_id', $user->resident_id)
                ->select('status')->selectRaw('COUNT(*) as total')
                ->groupBy('status')->pluck('total', 'status');
            $incidentCounts = [
                'total' => (int) $incidentStatusTotals->sum(),
                'active' => (int) collect(IncidentReport::ACTIVE_STATUSES)
                    ->sum(fn (string $status): int => (int) ($incidentStatusTotals[$status] ?? 0)),
            ];
            $query = ServiceRequest::query()->where('resident_id', $user->resident_id);
            $latestReviewedRequest = (clone $query)
                ->whereIn('status', [ServiceRequest::STATUS_AWAITING_PAYMENT, ServiceRequest::STATUS_COMPLETED, ServiceRequest::STATUS_DECLINED])
                ->whereNotNull('reviewed_at')->latest('reviewed_at')->latest('id')->first();
            $statusTotals = (clone $query)->select('status')->selectRaw('COUNT(*) as total')
                ->groupBy('status')->pluck('total', 'status');
            $requestCounts = [
                'total' => (int) $statusTotals->sum(),
                'pending' => (int) ($statusTotals[ServiceRequest::STATUS_PENDING] ?? 0),
                'awaiting_payment' => (int) ($statusTotals[ServiceRequest::STATUS_AWAITING_PAYMENT] ?? 0),
                'completed' => (int) ($statusTotals[ServiceRequest::STATUS_COMPLETED] ?? 0),
                'declined' => (int) ($statusTotals[ServiceRequest::STATUS_DECLINED] ?? 0),
            ];

            if ($latestReviewedRequest !== null) {
                $query->where('id', '!=', $latestReviewedRequest->id);
            }

            $recentRequests = $query->latest('updated_at')->latest('id')
                ->limit($latestReviewedRequest === null ? 4 : 3)->get();
        }

        return view('account', compact('user', 'recentRequests', 'latestReviewedRequest', 'latestIncidentReport', 'requestCounts', 'incidentCounts'));
    }

    public function verify(VerifyResidentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated): void {
            $resident = Resident::findAvailableRegistrationCode($validated['registration_code']);

            if ($resident === null) {
                throw ValidationException::withMessages([
                    'registration_code' => 'The registration code is invalid or expired. Ask barangay staff for a new code.',
                ]);
            }

            $user = User::query()->lockForUpdate()->findOrFail($request->user()->id);

            if ($user->role !== 'resident' || $user->resident_id !== null) {
                abort(403);
            }

            $user->forceFill([
                'name' => $resident->full_name,
                'resident_id' => $resident->id,
            ])->save();

            $resident->clearRegistrationCode();
        });

        return redirect()->route('account')->with('success', 'Your account is now linked to your resident record.');
    }
}
