@extends('layouts.auth')

@section('title', 'Forgot Password')
@section('story-title', 'A simple way back to your account.')
@section('story-description', 'Start with the email linked to your Barangay Information System account.')

@section('story-detail')
    <div class="auth-feature">
        <span class="auth-feature-icon"><i data-lucide="user-round" aria-hidden="true"></i></span>
        <div><strong>Enter your account email</strong><span>Use the same address you normally use to sign in.</span></div>
    </div>
    <div class="auth-feature">
        <span class="auth-feature-icon"><i data-lucide="badge-check" aria-hidden="true"></i></span>
        <div><strong>Receive secure instructions</strong><span>We will send a time-limited reset link to your inbox.</span></div>
    </div>
    <div class="auth-feature">
        <span class="auth-feature-icon"><i data-lucide="arrow-right" aria-hidden="true"></i></span>
        <div><strong>Return to your account</strong><span>Choose a new password, then continue to your dashboard.</span></div>
    </div>
@endsection

@section('content')
    <a href="{{ route('login') }}" class="auth-back-link"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to sign in</a>

    <div class="auth-recovery-heading">
        <span class="auth-recovery-icon"><i data-lucide="badge-check" aria-hidden="true"></i></span>
        <div><div class="auth-panel-kicker">ACCOUNT RECOVERY</div><h1 id="forgot-password-title">Forgot your password?</h1></div>
    </div>
    <p class="auth-panel-intro">Enter your account email and we will send instructions for choosing a new password.</p>

    @if (session('status'))
        <div class="auth-alert auth-alert--success" role="status">{{ session('status') }}</div>
    @endif

    <form class="auth-form" method="POST" action="{{ route('password.email') }}" aria-labelledby="forgot-password-title" aria-describedby="recovery-availability-note">
        @csrf
        <div class="auth-field">
            <label class="auth-label" for="recovery_email">Email address <span class="auth-required" aria-hidden="true">*</span></label>
            <input class="auth-input" id="recovery_email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" inputmode="email" placeholder="you@example.com" @if ($errors->has('email')) aria-invalid="true" aria-describedby="recovery-email-error recovery-email-help recovery-availability-note" @else aria-describedby="recovery-email-help recovery-availability-note" @endif>
            @error('email')<p class="auth-error" id="recovery-email-error" role="alert">{{ $message }}</p>@enderror
            <p class="auth-input-help" id="recovery-email-help">Use the email address linked to your staff or resident account.</p>
        </div>

        <button class="auth-submit" type="submit"><span>Send reset link</span><i data-lucide="arrow-right" aria-hidden="true"></i></button>
    </form>

    <div class="auth-recovery-note" id="recovery-availability-note">
        <i data-lucide="info" aria-hidden="true"></i>
        <p><strong>Protecting your account</strong><span>For privacy, the confirmation message is the same whether or not an account matches the email address.</span></p>
    </div>

    <p class="auth-switch">Remembered your password? <a href="{{ route('login') }}">Return to sign in</a></p>
@endsection
