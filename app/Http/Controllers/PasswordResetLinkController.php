<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('forgot-password');
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink([
            'email' => $request->validated('email'),
        ]);

        if ($status !== Password::ResetLinkSent) {
            throw ValidationException::withMessages([
                'email' => match ($status) {
                    Password::InvalidUser => 'This email address is not registered.',
                    Password::ResetThrottled => 'A reset link was recently sent. Please wait before requesting another one.',
                    default => 'We could not send a password reset link. Please try again.',
                },
            ]);
        }

        return back()->with(
            'status',
            'A password reset link has been sent to your email address.',
        );
    }
}
