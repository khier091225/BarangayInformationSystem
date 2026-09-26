@extends('layouts.auth')

@section('title', 'Sign In')
@section('story-title', 'Barangay services, within reach.')
@section('story-description', 'A shared space for barangay staff and verified residents to manage requests and stay informed.')

@section('story-detail')
    <div class="auth-feature">
        <span class="auth-feature-icon"><i data-lucide="file-check-2" aria-hidden="true"></i></span>
        <div><strong>Request documents</strong><span>Submit clearance and certificate requests from your account.</span></div>
    </div>
    <div class="auth-feature">
        <span class="auth-feature-icon"><i data-lucide="notebook-pen" aria-hidden="true"></i></span>
        <div><strong>Report an incident</strong><span>Send blotter details for barangay staff to review.</span></div>
    </div>
    <div class="auth-feature">
        <span class="auth-feature-icon"><i data-lucide="badge-check" aria-hidden="true"></i></span>
        <div><strong>Track progress</strong><span>See request updates and messages in one place.</span></div>
    </div>
@endsection

@section('content')
    <div class="auth-panel-kicker">WELCOME BACK</div>
    <h1 id="login-title">Sign in</h1>
    <p class="auth-panel-intro">Enter your account details to continue to your dashboard.</p>

    @if (session('warning'))
        <div class="auth-alert" role="alert">{{ session('warning') }}</div>
    @endif

    <form class="auth-form" method="POST" action="{{ route('login.store') }}" aria-labelledby="login-title">
        @csrf
        <div class="auth-field">
            <label class="auth-label" for="email">Email address <span class="auth-required" aria-hidden="true">*</span></label>
            <input class="auth-input" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com" @if ($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
            @error('email')<p class="auth-error" id="email-error" role="alert">{{ $message }}</p>@enderror
        </div>
        <div class="auth-field">
            <label class="auth-label" for="password">Password <span class="auth-required" aria-hidden="true">*</span></label>
            <div class="auth-password-wrap">
                <input class="auth-input" id="password" name="password" type="password" required autocomplete="current-password" placeholder="Enter your password" @if ($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif>
                <button class="auth-password-toggle" type="button" data-password-toggle="password" data-password-label="password" aria-label="Show password" aria-pressed="false">Show</button>
            </div>
            @error('password')<p class="auth-error" id="password-error" role="alert">{{ $message }}</p>@enderror
        </div>
        <button class="auth-submit" type="submit"><span>Sign in</span><i data-lucide="arrow-right" aria-hidden="true"></i></button>
    </form>

    <p class="auth-switch">Verified resident without an account? <a href="{{ route('register') }}">Create one</a></p>
@endsection
