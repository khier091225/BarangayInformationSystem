@extends('layouts.app')

@section('title')
    File New Blotter | Barangay Information System
@endsection

@section('main-style', 'max-width: 800px;')

@section('breadcrumb')
    <a href="{{ route('blotters.index') }}" style="color: inherit; text-decoration: none;">Blotter Records</a>
    <i data-lucide="chevron-right"></i>
    <strong>File New Complaint</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <a href="{{ route('blotters.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #42634e; text-decoration: none; margin-bottom: 12px;">
            <i data-lucide="arrow-left"></i> Back to Blotters
        </a>
        <h1 style="font-size: 24px; color: #1e3a29;">File New Blotter Report</h1>
        <p style="color: #69786b; font-size: 13px;">Fill in the details of the complaint or incident below.</p>
    </div>

    <!-- Form Card -->
    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <form method="POST" action="{{ route('blotters.store') }}">
            @csrf

            <!-- Complainant & Respondent -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                <div>
                    <x-form.label for="complainant" required>Complainant Name</x-form.label>
                    <x-form.input type="text" name="complainant" value="{{ old('complainant') }}" required placeholder="Full name of complainant" />
                    <x-form.error :message="$errors->first('complainant')" />
                </div>

                <div>
                    <x-form.label for="respondent" required>Respondent Name</x-form.label>
                    <x-form.input type="text" name="respondent" value="{{ old('respondent') }}" required placeholder="Full name of respondent" />
                    <x-form.error :message="$errors->first('respondent')" />
                </div>
            </div>

            <!-- Date & Status -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                <div>
                    <x-form.label for="incident_date" required>Incident Date</x-form.label>
                    <x-form.input type="date" name="incident_date" value="{{ old('incident_date', date('Y-m-d')) }}" required />
                    <x-form.error :message="$errors->first('incident_date')" />
                </div>

                <div>
                    <x-form.label for="status" required>Status</x-form.label>
                    <x-form.select name="status" required>
                        <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending (Ongoing hearing)</option>
                        <option value="Settled" {{ old('status') == 'Settled' ? 'selected' : '' }}>Settled (Resolved)</option>
                        <option value="Dismissed" {{ old('status') == 'Dismissed' ? 'selected' : '' }}>Dismissed (Dropped/Dismissed)</option>
                    </x-form.select>
                    <x-form.error :message="$errors->first('status')" />
                </div>
            </div>

            <!-- Incident Narrative -->
            <div style="margin-bottom: 24px;">
                <x-form.label for="incident" required>Incident Details</x-form.label>
                <x-form.textarea name="incident" rows="5" required placeholder="Describe the incident narrative in detail...">{{ old('incident') }}</x-form.textarea>
                <x-form.error :message="$errors->first('incident')" />
            </div>

            <!-- Submit Buttons -->
            <x-form.actions :cancel-url="route('blotters.index')">
                <x-slot:submit>Save Blotter Report</x-slot:submit>
            </x-form.actions>
        </form>
    </div>
@endsection
