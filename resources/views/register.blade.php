@extends('layouts.auth')

@section('title', 'Create Account')
@section('story-title', 'Your resident account starts here.')
@section('story-description', 'Registration is open to residents whose identity has been confirmed by barangay staff.')

@section('story-detail')
    <div class="auth-feature">
        <span class="auth-feature-icon"><i data-lucide="badge-check" aria-hidden="true"></i></span>
        <div><strong>1. Verify with staff</strong><span>Visit barangay staff to confirm your resident record.</span></div>
    </div>
    <div class="auth-feature">
        <span class="auth-feature-icon"><i data-lucide="file-check-2" aria-hidden="true"></i></span>
        <div><strong>2. Use your one-time code</strong><span>Enter the code issued for your resident record.</span></div>
    </div>
    <div class="auth-feature">
        <span class="auth-feature-icon"><i data-lucide="notebook-pen" aria-hidden="true"></i></span>
        <div><strong>3. Access your dashboard</strong><span>Request documents and track updates after signing up.</span></div>
    </div>
@endsection

@section('content')
    <div class="auth-panel-kicker">VERIFIED RESIDENTS</div>
    <h1 id="register-title">Create an account</h1>
    <p class="auth-panel-intro">Already verified by barangay staff? Use the code they gave you to get started.</p>

    <form class="auth-form" method="POST" action="{{ route('register.store') }}" aria-labelledby="register-title">
        @csrf
        <div class="auth-field">
            <label class="auth-label" for="registration_code">Registration code <span class="auth-required" aria-hidden="true">*</span></label>
            <input class="auth-input" id="registration_code" name="registration_code" type="text" required autofocus autocomplete="off" autocapitalize="characters" spellcheck="false" maxlength="19" placeholder="XXXX-XXXX-XXXX-XXXX" aria-describedby="registration-code-help @if ($errors->has('registration_code')) registration-code-error @endif" @if ($errors->has('registration_code')) aria-invalid="true" @endif>
            <p class="auth-input-help" id="registration-code-help">Ask barangay staff for your one-time code. It is valid for 24 hours.</p>
            @error('registration_code')<p class="auth-error" id="registration-code-error" role="alert">{{ $message }}</p>@enderror
        </div>
        <div class="auth-field">
            <label class="auth-label" for="email">Email address <span class="auth-required" aria-hidden="true">*</span></label>
            <input class="auth-input" id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com" @if ($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
            @error('email')<p class="auth-error" id="email-error" role="alert">{{ $message }}</p>@enderror
        </div>
        <div class="auth-field">
            <label class="auth-label" for="password">Password <span class="auth-required" aria-hidden="true">*</span></label>
            <div class="auth-password-wrap">
                <input class="auth-input" id="password" name="password" type="password" required minlength="8" autocomplete="new-password" placeholder="Create a password" aria-describedby="password-help @if ($errors->has('password')) password-error @endif" @if ($errors->has('password')) aria-invalid="true" @endif>
                <button class="auth-password-toggle" type="button" data-password-toggle="password" data-password-label="password" aria-label="Show password" aria-pressed="false">Show</button>
            </div>
            <p class="auth-input-help" id="password-help">Use at least 8 characters.</p>
            @error('password')<p class="auth-error" id="password-error" role="alert">{{ $message }}</p>@enderror
        </div>
        <div class="auth-field">
            <label class="auth-label" for="password_confirmation">Confirm password <span class="auth-required" aria-hidden="true">*</span></label>
            <div class="auth-password-wrap">
                <input class="auth-input" id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" placeholder="Re-enter your password">
                <button class="auth-password-toggle" type="button" data-password-toggle="password_confirmation" data-password-label="password confirmation" aria-label="Show password confirmation" aria-pressed="false">Show</button>
            </div>
        </div>
        <button class="auth-submit" type="submit"><span>Create account</span><i data-lucide="arrow-right" aria-hidden="true"></i></button>
    </form>

    <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
@endsection
