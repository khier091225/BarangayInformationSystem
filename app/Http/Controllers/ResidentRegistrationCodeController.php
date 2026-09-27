<?php

namespace App\Http\Controllers;

use App\Http\RegistrationCodeSms;
use App\Models\Resident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResidentRegistrationCodeController extends Controller
{
    public function store(Request $request, Resident $resident, RegistrationCodeSms $sms): RedirectResponse
    {
        $request->validate(['identity_confirmed' => 'accepted']);

        $code = strtoupper(bin2hex(random_bytes(8)));
        $formattedCode = implode('-', str_split($code, 4));

        $phoneNumber = DB::transaction(function () use ($resident, $code, $sms): string {
            $lockedResident = Resident::query()->lockForUpdate()->findOrFail($resident->id);

            if ($lockedResident->user()->exists()) {
                throw ValidationException::withMessages([
                    'registration_code' => 'This resident already has an account.',
                ]);
            }

            $phoneNumber = $sms->normalizePhoneNumber($lockedResident->contact_number);

            if ($phoneNumber === null) {
                throw ValidationException::withMessages([
                    'registration_code' => 'Add a valid Philippine mobile number to this resident record before sending a registration code.',
                ]);
            }

            if (! $sms->isConfigured()) {
                throw ValidationException::withMessages([
                    'registration_code' => 'SMS sending is not configured. Ask the system administrator to connect the SMS service.',
                ]);
            }

            if ($lockedResident->registration_code_issued_at?->isAfter(now()->subMinute())) {
                throw ValidationException::withMessages([
                    'registration_code' => 'Please wait one minute after the previous code was issued before sending a new one.',
                ]);
            }

            $lockedResident->forceFill([
                'registration_code_hash' => hash('sha256', $code),
                'registration_code_expires_at' => now()->addDay(),
                'registration_code_issued_at' => now(),
                'registration_code_sms_status' => 'unconfirmed',
            ])->save();

            return $phoneNumber;
        });

        $status = $sms->send($phoneNumber, $formattedCode);

        Resident::query()->whereKey($resident->id)
            ->where('registration_code_hash', hash('sha256', $code))
            ->update(['registration_code_sms_status' => $status]);

        if ($resident->fresh()?->registration_code_hash !== hash('sha256', $code)) {
            return redirect()->route('residents.show', $resident)
                ->with('warning', 'The registration code changed or was used while sending. Check the current registration status before trying again.');
        }

        return redirect()->route('residents.show', $resident)
            ->with([
                'registration_code' => $formattedCode,
                'registration_resident_id' => $resident->id,
            ]);
    }
}
