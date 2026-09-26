<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated): User {
            $resident = Resident::findAvailableRegistrationCode($validated['registration_code']);

            if ($resident === null) {
                throw ValidationException::withMessages([
                    'registration_code' => 'The registration code is invalid or expired. Ask barangay staff for a new code.',
                ]);
            }

            $user = User::create([
                'name' => $resident->full_name,
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'resident',
                'resident_id' => $resident->id,
            ]);

            $resident->clearRegistrationCode();

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account');
    }
}
