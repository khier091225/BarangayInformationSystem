@extends('layouts.resident')

@section('title', 'My Profile | Barangay Information System')

@section('main-class', 'resident-profile-page')

@section('content')
    <header class="resident-page-heading">
        <div>
            <a href="{{ route('account') }}" class="resident-page-back"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to dashboard</a>
            <span class="resident-kicker resident-kicker-dark">ACCOUNT SETTINGS</span>
            <h1>My profile</h1>
            <p>Review the information linked to your barangay record and keep your account password secure.</p>
        </div>
        <span class="resident-account-state"><i data-lucide="badge-check" aria-hidden="true"></i> Verified resident</span>
    </header>

    <div class="resident-profile-grid">
        <section class="resident-card resident-profile-details" aria-labelledby="resident-profile-details-title">
            <div class="resident-profile-summary">
                <span class="resident-profile-avatar"><i data-lucide="user-round" aria-hidden="true"></i></span>
                <div><strong>{{ $user->resident->full_name }}</strong><span>{{ $user->email }}</span></div>
            </div>

            <div class="resident-card-heading">
                <div><span class="resident-kicker resident-kicker-dark">VERIFIED INFORMATION</span><h2 id="resident-profile-details-title">Resident details</h2><p>These details come from the official barangay resident record.</p></div>
            </div>

            <dl class="resident-profile-list">
                <div><dt>Full name</dt><dd>{{ $user->resident->full_name }}</dd></div>
                <div><dt>Email address</dt><dd>{{ $user->email }}</dd></div>
                <div><dt>Mobile number</dt><dd>{{ $user->resident->contact_number ?: 'Not provided' }}</dd></div>
                <div><dt>Address</dt><dd>{{ $user->resident->address }}</dd></div>
            </dl>

            <div class="resident-profile-readonly"><i data-lucide="info" aria-hidden="true"></i><span><strong>Profile details are read-only</strong><span>Contact barangay staff if your name or resident information needs correction.</span></span></div>
        </section>

        <section class="resident-card resident-profile-password" aria-labelledby="resident-profile-password-title">
            <div class="resident-card-heading">
                <div><span class="resident-kicker resident-kicker-dark">SECURITY</span><h2 id="resident-profile-password-title">Change password</h2><p>Confirm your current password before choosing a new one.</p></div>
                <span class="resident-section-icon"><i data-lucide="badge-check" aria-hidden="true"></i></span>
            </div>

            <form method="POST" action="{{ route('account.profile.password.update') }}" class="resident-form">
                @csrf
                @method('PATCH')
                <div class="resident-field">
                    <x-form.label for="current_password" required>Current password</x-form.label>
                    <div class="resident-profile-password-control">
                        <x-form.input name="current_password" type="password" required autocomplete="current-password" :aria-invalid="$errors->password->has('current_password') ? 'true' : 'false'" :aria-describedby="$errors->password->has('current_password') ? 'resident-current-password-error' : null" />
                        <button type="button" class="resident-profile-password-toggle" data-password-toggle="current_password" data-password-label="current password" aria-label="Show current password" aria-controls="current_password" aria-pressed="false">Show</button>
                    </div>
                    <x-form.error id="resident-current-password-error" role="alert" :message="$errors->password->first('current_password')" />
                </div>

                <div class="resident-form-fields resident-profile-password-fields">
                    <div class="resident-field">
                        <x-form.label for="password" required>New password</x-form.label>
                        <div class="resident-profile-password-control">
                            <x-form.input name="password" type="password" required minlength="8" maxlength="72" autocomplete="new-password" :aria-invalid="$errors->password->has('password') ? 'true' : 'false'" :aria-describedby="$errors->password->has('password') ? 'resident-password-help resident-password-error' : 'resident-password-help'" />
                            <button type="button" class="resident-profile-password-toggle" data-password-toggle="password" data-password-label="new password" aria-label="Show new password" aria-controls="password" aria-pressed="false">Show</button>
                        </div>
                        <p class="resident-profile-help" id="resident-password-help">Use 8–72 characters, different from your current password.</p>
                        <x-form.error id="resident-password-error" role="alert" :message="$errors->password->first('password')" />
                    </div>
                    <div class="resident-field">
                        <x-form.label for="password_confirmation" required>Confirm new password</x-form.label>
                        <div class="resident-profile-password-control">
                            <x-form.input name="password_confirmation" type="password" required minlength="8" maxlength="72" autocomplete="new-password" :aria-invalid="$errors->password->has('password_confirmation') ? 'true' : 'false'" :aria-describedby="$errors->password->has('password_confirmation') ? 'resident-password-confirmation-error' : null" />
                            <button type="button" class="resident-profile-password-toggle" data-password-toggle="password_confirmation" data-password-label="password confirmation" aria-label="Show password confirmation" aria-controls="password_confirmation" aria-pressed="false">Show</button>
                        </div>
                        <x-form.error id="resident-password-confirmation-error" role="alert" :message="$errors->password->first('password_confirmation')" />
                    </div>
                </div>

                <div class="resident-form-actions"><button type="submit" class="resident-button resident-button-primary"><i data-lucide="badge-check" aria-hidden="true"></i> Change password</button></div>
            </form>
        </section>
    </div>
@endsection
