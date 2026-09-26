<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyResidentRequest;
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
        $requestCounts = ['total' => 0, 'pending' => 0, 'completed' => 0, 'declined' => 0];

        if ($user->role === 'resident' && $user->resident_id !== null) {
            $query = ServiceRequest::query()->where('resident_id', $user->resident_id);
            $recentRequests = (clone $query)->latest('updated_at')->latest('id')->limit(5)->get();
            $latestReviewedRequest = (clone $query)
                ->whereIn('status', [ServiceRequest::STATUS_COMPLETED, ServiceRequest::STATUS_DECLINED])
                ->whereNotNull('reviewed_at')->latest('reviewed_at')->latest('id')->first();
            $statusTotals = (clone $query)->select('status')->selectRaw('COUNT(*) as total')
                ->groupBy('status')->pluck('total', 'status');
            $requestCounts = [
                'total' => (int) $statusTotals->sum(),
                'pending' => (int) ($statusTotals[ServiceRequest::STATUS_PENDING] ?? 0),
                'completed' => (int) ($statusTotals[ServiceRequest::STATUS_COMPLETED] ?? 0),
                'declined' => (int) ($statusTotals[ServiceRequest::STATUS_DECLINED] ?? 0),
            ];
        }

        return view('account', compact('user', 'recentRequests', 'requestCounts', 'latestReviewedRequest'));
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
