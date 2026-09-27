@extends('layouts.auth')

@section('title', 'Reset Password')
@section('story-title', 'Secure your account with a new password.')
@section('story-description', 'Create a new password for your Barangay Information System account.')

@section('story-detail')
    <div class="auth-feature">
        <span class="auth-feature-icon"><i data-lucide="badge-check" aria-hidden="true"></i></span>
        <div><strong>Verified recovery link</strong><span>Your link is checked before any account details are changed.</span></div>
    </div>
    <div class="auth-feature">
        <span class="auth-feature-icon"><i data-lucide="user-round" aria-hidden="true"></i></span>
        <div><strong>Choose a private password</strong><span>Use at least eight characters that are difficult to guess.</span></div>
    </div>
    <div class="auth-feature">
        <span class="auth-feature-icon"><i data-lucide="arrow-right" aria-hidden="true"></i></span>
        <div><strong>Return to sign in</strong><span>Use your new password the next time you access your account.</span></div>
    </div>
@endsection

@section('content')
    <a href="{{ route('login') }}" class="auth-back-link"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to sign in</a>

    <div class="auth-recovery-heading">
        <span class="auth-recovery-icon"><i data-lucide="badge-check" aria-hidden="true"></i></span>
        <div><div class="auth-panel-kicker">ACCOUNT RECOVERY</div><h1 id="reset-password-title">Create a new password</h1></div>
    </div>
    <p class="auth-panel-intro">Enter and confirm the new password you want to use for this account.</p>

    <form class="auth-form" method="POST" action="{{ route('password.update') }}" aria-labelledby="reset-password-title">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="auth-field">
            <label class="auth-label" for="email">Email address <span class="auth-required" aria-hidden="true">*</span></label>
            <input class="auth-input" id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="username" inputmode="email" placeholder="you@example.com" @if ($errors->has('email')) aria-invalid="true" aria-describedby="reset-email-error" @endif>
            @error('email')<p class="auth-error" id="reset-email-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password">New password <span class="auth-required" aria-hidden="true">*</span></label>
            <div class="auth-password-wrap">
                <input class="auth-input" id="password" name="password" type="password" required autocomplete="new-password" placeholder="At least 8 characters" @if ($errors->has('password')) aria-invalid="true" aria-describedby="new-password-error new-password-help" @else aria-describedby="new-password-help" @endif>
                <button class="auth-password-toggle" type="button" data-password-toggle="password" data-password-label="new password" aria-label="Show new password" aria-pressed="false">Show</button>
            </div>
            @error('password')<p class="auth-error" id="new-password-error" role="alert">{{ $message }}</p>@enderror
            <p class="auth-input-help" id="new-password-help">Use at least 8 characters and avoid names or easy-to-guess words.</p>
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password_confirmation">Confirm new password <span class="auth-required" aria-hidden="true">*</span></label>
            <div class="auth-password-wrap">
                <input class="auth-input" id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Enter the password again" @if ($errors->has('password_confirmation')) aria-invalid="true" aria-describedby="password-confirmation-error" @endif>
                <button class="auth-password-toggle" type="button" data-password-toggle="password_confirmation" data-password-label="password confirmation" aria-label="Show password confirmation" aria-pressed="false">Show</button>
            </div>
            @error('password_confirmation')<p class="auth-error" id="password-confirmation-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <button class="auth-submit" type="submit"><span>Reset password</span><i data-lucide="arrow-right" aria-hidden="true"></i></button>
    </form>

    <p class="auth-switch">Need a new recovery link? <a href="{{ route('password.request') }}">Request another</a></p>
@endsection
