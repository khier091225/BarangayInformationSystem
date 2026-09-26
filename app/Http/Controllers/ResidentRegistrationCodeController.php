<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResidentRegistrationCodeController extends Controller
{
    public function store(Request $request, Resident $resident): RedirectResponse
    {
        $request->validate(['identity_confirmed' => 'accepted']);

        $code = strtoupper(bin2hex(random_bytes(8)));

        DB::transaction(function () use ($resident, $code): void {
            $lockedResident = Resident::query()->lockForUpdate()->findOrFail($resident->id);

            if ($lockedResident->user()->exists()) {
                throw ValidationException::withMessages([
                    'registration_code' => 'This resident already has an account.',
                ]);
            }

            $lockedResident->forceFill([
                'registration_code_hash' => hash('sha256', $code),
                'registration_code_expires_at' => now()->addDay(),
            ])->save();
        });

        return redirect()->route('residents.show', $resident)
            ->with([
                'registration_code' => implode('-', str_split($code, 4)),
                'registration_resident_id' => $resident->id,
            ]);
    }
}
