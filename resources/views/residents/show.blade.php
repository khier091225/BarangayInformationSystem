@extends('layouts.app')

@section('title')
    {{ $resident->full_name }} | Barangay Information System
@endsection

@section('breadcrumb')
    <a href="{{ route('residents.index') }}" style="color: inherit; text-decoration: none;">Residents</a>
    <i data-lucide="chevron-right"></i>
    <strong>{{ $resident->full_name }}</strong>
@endsection

@section('content')
    <x-workspace.page-header :title="$resident->full_name" description="Review the resident profile, household information, and account registration." icon="user-round">
        <x-slot:actions>
            <a href="{{ route('residents.edit', [$resident]) }}" class="button button-primary" data-record-dialog-trigger aria-haspopup="dialog" aria-controls="resident-edit-dialog-{{ $resident->getKey() }}"><i data-lucide="pencil" aria-hidden="true"></i> Edit resident</a>
            <form method="POST" action="{{ route('residents.destroy', [$resident]) }}" onsubmit="return confirm('Are you sure you want to delete this resident record?');" data-confirm-title="Delete resident?" data-confirm-message="Delete the resident record for {{ $resident->full_name }}?" data-confirm-label="Delete resident">
                @csrf
                @method('DELETE')
                <button type="submit" class="record-delete record-delete-large">Delete</button>
            </form>
        </x-slot:actions>
    </x-workspace.page-header>

    <section class="registration-card" aria-labelledby="registration-title">
        <h2 id="registration-title">Resident account registration</h2>
        @if ($resident->user)
            <p style="font-size: 13px; color: var(--muted); margin: 0;">An account is linked to this resident: <strong>{{ $resident->user->email }}</strong></p>
        @else
            <p class="registration-intro">Verify the resident's identity and mobile number, then send their registration code by SMS. Each code works once and expires after 24 hours.</p>

            <div class="registration-recipient">
                <div><span>REGISTERED MOBILE NUMBER</span><strong>{{ $registrationPhoneNumber ? '+'.$registrationPhoneNumber : ($resident->contact_number ?: 'No mobile number on record') }}</strong></div>
                <a href="{{ route('residents.edit', $resident) }}" data-record-dialog-trigger aria-haspopup="dialog" aria-controls="resident-edit-dialog-{{ $resident->getKey() }}">Update number <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
            </div>

            @if (! $registrationPhoneNumber)
                <p class="registration-notice">Add a valid Philippine mobile number to this resident's profile before sending. Accepted formats include 09171234567 and +639171234567.</p>
            @endif
            @if (! $registrationSmsConfigured)
                <p class="registration-notice">SMS sending is not configured. Ask the system administrator to connect the SMS service.</p>
            @endif

            @if ($resident->registration_code_expires_at?->isFuture())
                @if ($resident->registration_code_sms_status === 'submitted')
                    <div class="registration-notice registration-notice-success" role="status"><strong>SMS submitted</strong><span>The SMS service accepted the registration code. Delivery may take a moment; ask the resident to check their phone.</span></div>
                @elseif ($resident->registration_code_sms_status === 'failed')
                    <div class="registration-notice" role="alert"><strong>SMS could not be sent</strong><span>The code is still active. If it is shown below, give it only to the verified resident. Contact the system administrator before sending a new code.</span></div>
                @elseif ($resident->registration_code_sms_status === 'unconfirmed')
                    <div class="registration-notice" role="alert"><strong>SMS submission not confirmed</strong><span>The SMS service did not confirm the request. The code is still active and the SMS may still arrive. Check the resident's phone before sending a new code.</span></div>
                @endif
                <p class="registration-help">Current code expires {{ $resident->registration_code_expires_at->format('M d, Y h:i A') }}. Sending a new code cancels the previous one.</p>
            @endif

            @if (session('registration_code') && session('registration_resident_id') === $resident->id)
                <div class="registration-code-preview">
                    <strong>Registration code</strong>
                    <code>{{ session('registration_code') }}</code>
                    <span>Shown here once. If SMS is unavailable, give this code only to the verified resident.</span>
                </div>
            @endif

            <x-form.error role="alert" :message="$errors->first('registration_code')" />
            <form method="POST" action="{{ route('residents.registration-code.store', $resident) }}" data-registration-code-form>
                @csrf
                <label class="registration-confirmation">
                    <input type="checkbox" name="identity_confirmed" value="1" required @if ($errors->has('identity_confirmed')) aria-invalid="true" aria-describedby="registration-identity-error" @endif>
                    <span>I have verified this resident's identity and confirmed that the mobile number belongs to them.</span>
                </label>
                <x-form.error id="registration-identity-error" role="alert" :message="$errors->first('identity_confirmed')" />
                <div class="registration-actions">
                    <button type="submit" class="button button-primary" @disabled(! $registrationPhoneNumber || ! $registrationSmsConfigured)>{{ $resident->registration_code_expires_at?->isFuture() ? 'Send a new code by SMS' : 'Send registration code by SMS' }}</button>
                    <span class="registration-help">Wait at least one minute between sends.</span>
                </div>
            </form>
        @endif
    </section>

    <!-- Info Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap: 16px; margin-bottom: 24px;">
        <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 18px;">
            <span style="font-size: 11px; color: var(--muted-soft); text-transform: uppercase; font-weight: 600;">Gender</span>
            <div style="font-size: 17px; font-weight: 600; color: var(--ink); margin-top: 4px;">{{ $resident->gender }}</div>
        </div>

        <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 18px;">
            <span style="font-size: 11px; color: var(--muted-soft); text-transform: uppercase; font-weight: 600;">Age & Birthdate</span>
            <div style="font-size: 17px; font-weight: 600; color: var(--ink); margin-top: 4px;">
                {{ $resident->birthdate ? $resident->birthdate->age . ' yrs old' : 'N/A' }}
            </div>
            <div style="font-size: 11px; color: var(--muted-soft); margin-top: 2px;">
                {{ $resident->birthdate ? $resident->birthdate->format('F d, Y') : '-' }}
            </div>
        </div>

        <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 18px;">
            <span style="font-size: 11px; color: var(--muted-soft); text-transform: uppercase; font-weight: 600;">Civil Status</span>
            <div style="font-size: 17px; font-weight: 600; color: var(--ink); margin-top: 4px;">{{ $resident->civil_status }}</div>
        </div>

        <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 18px;">
            <span style="font-size: 11px; color: var(--muted-soft); text-transform: uppercase; font-weight: 600;">Voter Status</span>
            <div style="margin-top: 6px;">
                @if ($resident->is_voter)
                    <span style="background: var(--success-soft); color: var(--success); padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">Registered</span>
                @else
                    <span style="background: var(--surface-soft); color: var(--muted-soft); padding: 4px 10px; border-radius: 4px; font-size: 12px;">Not registered</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Household & Contact Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); gap: 20px; margin-bottom: 24px;">
        <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 22px;">
            <span style="font-size: 11px; color: var(--muted-soft); text-transform: uppercase; font-weight: 600;">Household Information</span>
            @if($resident->household)
                <div style="margin-top: 10px;">
                    <div style="font-size: 18px; font-weight: 700; color: var(--accent);">
                        <a href="{{ route('households.show', [$resident->household]) }}" style="color: inherit; text-decoration: underline;">
                            {{ $resident->household->household_number }}
                        </a>
                    </div>
                    <div style="font-size: 13px; color: var(--muted); margin-top: 4px;">
                        <strong>Household Head:</strong> {{ $resident->household->household_head }}
                    </div>
                    <div style="font-size: 13px; color: var(--muted); margin-top: 2px;">
                        <strong>Address:</strong> {{ $resident->household->address }}
                    </div>
                </div>
            @else
                <div style="margin-top: 10px; color: var(--muted); font-style: italic; font-size: 13px;">
                    No household linked to this resident.
                </div>
            @endif
        </div>

        <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 22px;">
            <span style="font-size: 11px; color: var(--muted-soft); text-transform: uppercase; font-weight: 600;">Contact & Residential Address</span>
            <div style="margin-top: 10px;">
                <div style="font-size: 14px; color: var(--ink); margin-bottom: 6px;">
                    <strong>Residential Address:</strong> {{ $resident->address }}
                </div>
                <div style="font-size: 14px; color: var(--ink);">
                    <strong>Contact Phone:</strong> {{ $resident->contact_number ?: 'None provided' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Certificates History Table -->
    <x-workspace.table-scroll label="Issued certificates">
        <x-slot:heading>
            <div style="padding: 16px 20px; border-bottom: 1px solid var(--line); background: var(--canvas); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 15px; color: var(--ink); margin: 0;">Issued certificates</h3>
            </div>
        </x-slot:heading>

        <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: var(--canvas); border-bottom: 1px solid var(--line); text-align: left; color: var(--muted);">
                    <th style="padding: 12px 16px;">Certificate Type</th>
                    <th style="padding: 12px 16px;">Purpose</th>
                    <th style="padding: 12px 16px;">Issued Date</th>
                    <th style="padding: 12px 16px;">Fee</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($resident->certificates as $certificate)
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 12px 16px; font-weight: 600; color: var(--ink);">{{ $certificate->certificate_type }}</td>
                        <td style="padding: 12px 16px; color: var(--muted);">{{ $certificate->purpose }}</td>
                        <td style="padding: 12px 16px; color: var(--muted);">{{ $certificate->issued_date ? $certificate->issued_date->format('M d, Y') : '-' }}</td>
                        <td style="padding: 12px 16px; font-weight: 600;">₱{{ number_format($certificate->fee, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 28px; color: var(--muted-soft);">
                            No certificates issued yet for this resident.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-workspace.table-scroll>

    <x-record-dialog :id="'resident-edit-dialog-'.$resident->getKey()" title="Edit resident" :description="'Update the details for '.$resident->full_name.'.'" icon="users-round" :wide="true" :open-on-load="$errors->any() && old('_record_form') === 'residents.edit.'.$resident->getKey()">
        <x-residents.form :resident="$resident" :households="$households" :modal="true" :return-to-profile="true" />
    </x-record-dialog>
@endsection
