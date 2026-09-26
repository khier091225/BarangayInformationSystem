@extends('layouts.app')

@section('title', 'My Profile | Barangay Information System')

@section('breadcrumb')
    <button type="button" class="icon-button sidebar-toggle" aria-label="Open sidebar" aria-controls="workspace-navigation" aria-expanded="false"><i data-lucide="panel-left" aria-hidden="true"></i></button>
    <span>Staff workspace</span><i data-lucide="chevron-right" aria-hidden="true"></i><strong>My profile</strong>
@endsection

@section('content')
    <div class="staff-profile">
        <header class="overview-heading">
            <div><span class="overview-eyebrow">ACCOUNT SETTINGS</span><h1>My profile</h1><p>Keep your account details up to date and manage your password.</p></div>
        </header>

        <div class="profile-summary">
            <span class="profile-avatar"><i data-lucide="user-round" aria-hidden="true"></i></span>
            <div><strong>{{ $user->name }}</strong><span>{{ $user->email }}</span></div>
            <span class="profile-role"><i data-lucide="badge-check" aria-hidden="true"></i> Barangay staff</span>
        </div>

        <section class="profile-panel" aria-labelledby="profile-details-title">
            <div class="profile-panel-heading"><span class="profile-section-icon"><i data-lucide="user-round" aria-hidden="true"></i></span><div><h2 id="profile-details-title">Personal information</h2><p>Update your name and the email you use to sign in.</p></div></div>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')
                <div class="profile-fields">
                    <div class="profile-field">
                        <x-form.label for="name" required>Full name</x-form.label>
                        <x-form.input name="name" :value="old('name', $user->name)" required maxlength="255" autocomplete="name" :aria-invalid="$errors->profile->has('name') ? 'true' : 'false'" :aria-describedby="$errors->profile->has('name') ? 'name-error' : null" />
                        <x-form.error id="name-error" :message="$errors->profile->first('name')" />
                    </div>
                    <div class="profile-field">
                        <x-form.label for="email" required>Email address</x-form.label>
                        <x-form.input type="email" name="email" :value="old('email', $user->email)" required maxlength="255" autocomplete="email" :aria-invalid="$errors->profile->has('email') ? 'true' : 'false'" :aria-describedby="$errors->profile->has('email') ? 'email-help email-error' : 'email-help'" />
                        <p class="profile-help" id="email-help">Use your updated email the next time you sign in.</p>
                        <x-form.error id="email-error" :message="$errors->profile->first('email')" />
                    </div>
                </div>
                <div class="profile-form-actions"><button type="submit" class="overview-button overview-button-primary"><i data-lucide="check" aria-hidden="true"></i> Save profile</button></div>
            </form>
        </section>

        <section class="profile-panel" aria-labelledby="profile-password-title">
            <div class="profile-panel-heading"><span class="profile-section-icon"><i data-lucide="badge-check" aria-hidden="true"></i></span><div><h2 id="profile-password-title">Change password</h2><p>Confirm your current password before choosing a new one.</p></div></div>
            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                @method('PATCH')
                <div class="profile-fields">
                    <div class="profile-field profile-field-wide">
                        <x-form.label for="current_password" required>Current password</x-form.label>
                        <div class="profile-password-control">
                            <x-form.input name="current_password" type="password" required autocomplete="current-password" :aria-invalid="$errors->password->has('current_password') ? 'true' : 'false'" :aria-describedby="$errors->password->has('current_password') ? 'current-password-error' : null" />
                            <button type="button" class="profile-password-toggle" data-password-toggle="current_password" data-password-label="current password" aria-label="Show current password" aria-controls="current_password" aria-pressed="false">Show</button>
                        </div>
                        <x-form.error id="current-password-error" :message="$errors->password->first('current_password')" />
                    </div>
                    <div class="profile-field">
                        <x-form.label for="password" required>New password</x-form.label>
                        <div class="profile-password-control">
                            <x-form.input name="password" type="password" required minlength="8" maxlength="72" autocomplete="new-password" :aria-invalid="$errors->password->has('password') ? 'true' : 'false'" :aria-describedby="$errors->password->has('password') ? 'password-help password-error' : 'password-help'" />
                            <button type="button" class="profile-password-toggle" data-password-toggle="password" data-password-label="new password" aria-label="Show new password" aria-controls="password" aria-pressed="false">Show</button>
                        </div>
                        <p class="profile-help" id="password-help">Use 8–72 characters, different from your current password.</p>
                        <x-form.error id="password-error" :message="$errors->password->first('password')" />
                    </div>
                    <div class="profile-field">
                        <x-form.label for="password_confirmation" required>Confirm new password</x-form.label>
                        <div class="profile-password-control">
                            <x-form.input name="password_confirmation" type="password" required minlength="8" maxlength="72" autocomplete="new-password" :aria-invalid="$errors->password->has('password_confirmation') ? 'true' : 'false'" :aria-describedby="$errors->password->has('password_confirmation') ? 'password-confirmation-error' : null" />
                            <button type="button" class="profile-password-toggle" data-password-toggle="password_confirmation" data-password-label="password confirmation" aria-label="Show password confirmation" aria-controls="password_confirmation" aria-pressed="false">Show</button>
                        </div>
                        <x-form.error id="password-confirmation-error" :message="$errors->password->first('password_confirmation')" />
                    </div>
                </div>
                <div class="profile-form-actions"><button type="submit" class="overview-button overview-button-primary"><i data-lucide="badge-check" aria-hidden="true"></i> Change password</button></div>
            </form>
        </section>
    </div>
@endsection
