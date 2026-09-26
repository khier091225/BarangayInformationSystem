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
        $requestCounts = ['total' => 0, 'pending' => 0, 'completed' => 0];

        if ($user->role === 'resident' && $user->resident_id !== null) {
            $query = ServiceRequest::query()->where('resident_id', $user->resident_id);
            $recentRequests = (clone $query)->latest()->limit(5)->get();
            $requestCounts = [
                'total' => (clone $query)->count(),
                'pending' => (clone $query)->where('status', ServiceRequest::STATUS_PENDING)->count(),
                'completed' => (clone $query)->where('status', ServiceRequest::STATUS_COMPLETED)->count(),
            ];
        }

        return view('account', compact('user', 'recentRequests', 'requestCounts'));
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
