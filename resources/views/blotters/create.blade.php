@extends('layouts.app')

@section('title')
    Record Blotter | Barangay Information System
@endsection

@section('main-style', 'max-width: 800px;')

@section('breadcrumb')
    <a href="{{ route('blotters.index') }}" style="color: inherit; text-decoration: none;">Blotter Records</a>
    <i data-lucide="chevron-right"></i>
    <strong>Record blotter</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; color: var(--ink);">Record blotter</h1>
        <p style="color: var(--muted); font-size: 13px;">Record the complainant and what happened. New cases start as Pending.</p>
    </div>

    <div class="staff-request-detail-card">
        <form method="POST" action="{{ route('blotters.store') }}" class="complaint-staff-form">
            @csrf
            <div class="resident-field">
                <x-form.label for="complainant" required>Complainant name</x-form.label>
                <x-form.input name="complainant" :value="old('complainant')" required maxlength="255" placeholder="Full name of complainant" :aria-invalid="$errors->has('complainant') ? 'true' : 'false'" />
                <x-form.error :message="$errors->first('complainant')" />
            </div>
            <div class="resident-field">
                <x-form.label for="incident" required>What happened?</x-form.label>
                <x-form.textarea name="incident" rows="5" required placeholder="Describe the incident and where it happened." :aria-invalid="$errors->has('incident') ? 'true' : 'false'">{{ old('incident') }}</x-form.textarea>
                <x-form.error :message="$errors->first('incident')" />
            </div>
            <details class="complaint-optional-fields" @if ($errors->has('respondent') || $errors->has('incident_date')) open @endif>
                <summary>Add respondent or incident date <span>(optional)</span></summary>
                <div class="complaint-optional-body">
                    <div class="resident-field">
                        <x-form.label for="respondent">Respondent name</x-form.label>
                        <x-form.input name="respondent" :value="old('respondent')" maxlength="255" placeholder="Leave blank if unknown" :aria-invalid="$errors->has('respondent') ? 'true' : 'false'" />
                        <x-form.error :message="$errors->first('respondent')" />
                    </div>
                    <div class="resident-field">
                        <x-form.label for="incident_date">Date of incident</x-form.label>
                        <x-form.input name="incident_date" type="date" :value="old('incident_date')" :aria-invalid="$errors->has('incident_date') ? 'true' : 'false'" />
                        <p class="incident-field-help">Leave blank if it happened today.</p>
                        <x-form.error :message="$errors->first('incident_date')" />
                    </div>
                </div>
            </details>
            <x-form.actions :cancel-url="route('blotters.index')">
                <x-slot:submit>Save blotter record</x-slot:submit>
            </x-form.actions>
        </form>
    </div>
@endsection
