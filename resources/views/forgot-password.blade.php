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
        <div><strong>Receive secure instructions</strong><span>The completed recovery flow will send a time-limited reset link.</span></div>
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
    <p class="auth-panel-intro">Enter your account email. When recovery is connected, you will receive instructions for choosing a new password.</p>

    <form class="auth-form" method="POST" action="{{ route('password.request') }}" data-password-recovery-preview aria-labelledby="forgot-password-title" aria-describedby="recovery-availability-note">
        @csrf
        <div class="auth-field">
            <label class="auth-label" for="recovery_email">Email address <span class="auth-required" aria-hidden="true">*</span></label>
            <input class="auth-input" id="recovery_email" name="email" type="email" required autofocus autocomplete="email" inputmode="email" placeholder="you@example.com" aria-describedby="recovery-email-help recovery-availability-note">
            <p class="auth-input-help" id="recovery-email-help">Use the email address linked to your staff or resident account.</p>
        </div>

        <button class="auth-submit" type="submit"><span>Continue</span><i data-lucide="arrow-right" aria-hidden="true"></i></button>
    </form>

    <div class="auth-recovery-preview" data-password-recovery-status role="status" tabindex="-1" hidden>
        <span class="auth-recovery-preview-icon"><i data-lucide="info" aria-hidden="true"></i></span>
        <div><strong>Recovery screen ready</strong><p>Email delivery is not connected yet, so no message was sent and your account was not changed.</p></div>
    </div>

    <div class="auth-recovery-note" id="recovery-availability-note">
        <i data-lucide="info" aria-hidden="true"></i>
        <p><strong>Design-only screen</strong><span>Password recovery email and reset links will be connected in the next step.</span></p>
    </div>

    <p class="auth-switch">Remembered your password? <a href="{{ route('login') }}">Return to sign in</a></p>
@endsection
