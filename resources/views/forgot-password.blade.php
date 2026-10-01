@extends('layouts.auth')

@section('title', 'Forgot Password')
@section('story-title', 'A simple way back to your account.')
@section('story-description', 'Start with the email linked to your Barangay Information System account.')

@section('story-detail')
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
            <input class="auth-input" id="recovery_email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" inputmode="email" placeholder="you@example.com" @if ($errors->has('email')) aria-invalid="true" aria-describedby="recovery-email-error recovery-availability-note" @else aria-describedby="recovery-availability-note" @endif>
            @error('email')<p class="auth-error" id="recovery-email-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <button class="auth-submit" type="submit"><span>Send reset link</span><i data-lucide="arrow-right" aria-hidden="true"></i></button>
    </form>

    <div class="auth-recovery-note" id="recovery-availability-note">
        <i data-lucide="info" aria-hidden="true"></i>
        <p><strong>Use your registered email</strong><span>Enter the email address linked to your account. After requesting a reset link, check your inbox and spam folder.</span></p>
    </div>

@endsection
