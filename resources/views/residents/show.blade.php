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
    <div style="margin-bottom: 24px;">
        <a href="{{ route('residents.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #42634e; text-decoration: none; margin-bottom: 12px;">
            <i data-lucide="arrow-left"></i> Back to Residents
        </a>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div class="eyebrow">RESIDENT PROFILE</div>
                <h1 style="font-size: 26px; color: #1e3a29; margin-top: 4px;">{{ $resident->full_name }}</h1>
                <p style="color: #69786b; font-size: 13px;">Member of Barangay Information System community registry.</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('residents.edit', [$resident]) }}" class="button button-outline" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i data-lucide="pencil"></i> Edit Profile
                </a>
                <form method="POST" action="{{ route('residents.destroy', [$resident]) }}" onsubmit="return confirm('Are you sure you want to delete this resident record?');" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: 1px solid #eed0ce; color: #a43229; font-size: 13px; font-weight: 600; padding: 9px 14px; border-radius: 6px; cursor: pointer;">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <section style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 22px; margin-bottom: 24px;">
        <h2 style="font-size: 17px; color: #1e3a29; margin: 0 0 8px;">Resident account registration</h2>
        @if ($resident->user)
            <p style="font-size: 13px; color: #556658; margin: 0;">An account is linked to this resident: <strong>{{ $resident->user->email }}</strong></p>
        @else
            <p style="font-size: 13px; color: #556658; margin: 0 0 14px;">Verify the resident's identity before issuing a code. The code expires after 24 hours and works once.</p>

            @if (session('registration_code') && session('registration_resident_id') === $resident->id)
                <div role="status" style="background: #eaf5eb; border: 1px solid #c2e2c7; border-radius: 6px; padding: 14px; margin-bottom: 16px;">
                    <strong>Give this code to the resident now:</strong>
                    <div style="font-family: monospace; font-size: 20px; letter-spacing: 2px; margin: 8px 0; user-select: all;">{{ session('registration_code') }}</div>
                    <span style="font-size: 12px;">It will not be shown again. You can issue a new code if needed.</span>
                </div>
            @elseif ($resident->registration_code_expires_at?->isFuture())
                <p style="font-size: 13px; color: #704800; margin: 0 0 14px;">An active code was issued. It expires {{ $resident->registration_code_expires_at->format('M d, Y h:i A') }}. Issuing a new one cancels the old code.</p>
            @endif

            @error('registration_code')
                <p role="alert" style="font-size: 13px; color: #a43229; margin-bottom: 12px;">{{ $message }}</p>
            @enderror
            <form method="POST" action="{{ route('residents.registration-code.store', $resident) }}">
                @csrf
                <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #334a38; margin-bottom: 12px;">
                    <input type="checkbox" name="identity_confirmed" value="1" required style="margin-top: 2px;">
                    <span>I have verified this resident's identity.</span>
                </label>
                @error('identity_confirmed')
                    <p role="alert" style="font-size: 13px; color: #a43229; margin-bottom: 12px;">{{ $message }}</p>
                @enderror
                <button type="submit" class="button button-primary">{{ $resident->registration_code_expires_at?->isFuture() ? 'Issue a new code' : 'Issue registration code' }}</button>
            </form>
        @endif
    </section>

    <!-- Info Grid -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 18px;">
            <span style="font-size: 11px; color: #788577; text-transform: uppercase; font-weight: 600;">Gender</span>
            <div style="font-size: 17px; font-weight: 600; color: #1e3a29; margin-top: 4px;">{{ $resident->gender }}</div>
        </div>

        <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 18px;">
            <span style="font-size: 11px; color: #788577; text-transform: uppercase; font-weight: 600;">Age & Birthdate</span>
            <div style="font-size: 17px; font-weight: 600; color: #1e3a29; margin-top: 4px;">
                {{ $resident->birthdate ? $resident->birthdate->age . ' yrs old' : 'N/A' }}
            </div>
            <div style="font-size: 11px; color: #788577; margin-top: 2px;">
                {{ $resident->birthdate ? $resident->birthdate->format('F d, Y') : '-' }}
            </div>
        </div>

        <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 18px;">
            <span style="font-size: 11px; color: #788577; text-transform: uppercase; font-weight: 600;">Civil Status</span>
            <div style="font-size: 17px; font-weight: 600; color: #1e3a29; margin-top: 4px;">{{ $resident->civil_status }}</div>
        </div>

        <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 18px;">
            <span style="font-size: 11px; color: #788577; text-transform: uppercase; font-weight: 600;">Voter Status</span>
            <div style="margin-top: 6px;">
                @if ($resident->is_voter)
                    <span style="background: #eaf5eb; color: #236539; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">Registered Voter</span>
                @else
                    <span style="background: #f4f4f4; color: #777; padding: 4px 10px; border-radius: 4px; font-size: 12px;">Non-Voter</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Household & Contact Cards -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
        <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 22px;">
            <span style="font-size: 11px; color: #788577; text-transform: uppercase; font-weight: 600;">Household Information</span>
            @if($resident->household)
                <div style="margin-top: 10px;">
                    <div style="font-size: 18px; font-weight: 700; color: #276747;">
                        <a href="{{ route('households.show', [$resident->household]) }}" style="color: inherit; text-decoration: underline;">
                            {{ $resident->household->household_number }}
                        </a>
                    </div>
                    <div style="font-size: 13px; color: #556658; margin-top: 4px;">
                        <strong>Household Head:</strong> {{ $resident->household->household_head }}
                    </div>
                    <div style="font-size: 13px; color: #556658; margin-top: 2px;">
                        <strong>Address:</strong> {{ $resident->household->address }}
                    </div>
                </div>
            @else
                <div style="margin-top: 10px; color: #888; font-style: italic; font-size: 13px;">
                    No household linked to this resident.
                </div>
            @endif
        </div>

        <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 22px;">
            <span style="font-size: 11px; color: #788577; text-transform: uppercase; font-weight: 600;">Contact & Residential Address</span>
            <div style="margin-top: 10px;">
                <div style="font-size: 14px; color: #333; margin-bottom: 6px;">
                    <strong>Residential Address:</strong> {{ $resident->address }}
                </div>
                <div style="font-size: 14px; color: #333;">
                    <strong>Contact Phone:</strong> {{ $resident->contact_number ?: 'None provided' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Certificates History Table -->
    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <div style="padding: 16px 20px; border-bottom: 1px solid #edf1eb; background: #fbfdfa; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 15px; color: #1e3a29; margin: 0;">Issued Certificates & Clearances</h3>
        </div>

        <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f8faf7; border-bottom: 1px solid #e3e8e1; text-align: left; color: #5a6b5c;">
                    <th style="padding: 12px 16px;">Certificate Type</th>
                    <th style="padding: 12px 16px;">Purpose</th>
                    <th style="padding: 12px 16px;">Issued Date</th>
                    <th style="padding: 12px 16px;">Fee</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($resident->certificates as $certificate)
                    <tr style="border-bottom: 1px solid #edf1eb;">
                        <td style="padding: 12px 16px; font-weight: 600; color: #1e3a29;">{{ $certificate->certificate_type }}</td>
                        <td style="padding: 12px 16px; color: #556658;">{{ $certificate->purpose }}</td>
                        <td style="padding: 12px 16px; color: #556658;">{{ $certificate->issued_date ? $certificate->issued_date->format('M d, Y') : '-' }}</td>
                        <td style="padding: 12px 16px; font-weight: 600;">₱{{ number_format($certificate->fee, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 28px; color: #829283;">
                            No certificates issued yet for this resident.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
