@extends('layouts.resident')

@section('title', 'File a Blotter Report | Barangay Information System')
@section('main-class', 'resident-main-form')

@section('content')
    <a href="{{ route('account') }}" class="resident-page-back"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to dashboard</a>
    <div class="resident-page-heading">
        <div>
            <span class="resident-kicker resident-kicker-dark">INCIDENT REPORT</span>
            <h1>File a blotter report</h1>
            <p>Share the incident details below. Barangay staff will review your report before recording it in the official blotter.</p>
        </div>
    </div>

    <section class="resident-card resident-form-card resident-service-form" aria-label="Blotter report form">
        <div class="resident-form-intro">
            <span class="resident-section-icon"><i data-lucide="user-round" aria-hidden="true"></i></span>
            <div><span>Complainant</span><strong>{{ auth()->user()->name }}</strong></div>
            <p>Fields marked * are required.</p>
        </div>
        <form method="POST" action="{{ route('account.requests.blotter.store') }}" class="resident-form">
            @csrf
            <div class="resident-form-fields">
                <div class="resident-field">
                    <x-form.label for="respondent" required>Respondent name</x-form.label>
                    <x-form.input name="respondent" :value="old('respondent')" required maxlength="255" placeholder="Name of the person involved" :aria-invalid="$errors->has('respondent') ? 'true' : 'false'" :aria-describedby="$errors->has('respondent') ? 'respondent-error' : null" />
                    <x-form.error id="respondent-error" :message="$errors->first('respondent')" />
                </div>
                <div class="resident-field">
                    <x-form.label for="incident_date" required>Date of incident</x-form.label>
                    <x-form.input name="incident_date" type="date" :value="old('incident_date')" max="{{ today()->toDateString() }}" required :aria-invalid="$errors->has('incident_date') ? 'true' : 'false'" :aria-describedby="$errors->has('incident_date') ? 'incident-date-error' : null" />
                    <x-form.error id="incident-date-error" :message="$errors->first('incident_date')" />
                </div>
                <div class="resident-field resident-field-wide">
                    <x-form.label for="incident" required>What happened?</x-form.label>
                    <x-form.textarea name="incident" rows="5" required minlength="10" maxlength="5000" placeholder="Describe the incident, place, and people involved." :aria-invalid="$errors->has('incident') ? 'true' : 'false'" :aria-describedby="$errors->has('incident') ? 'incident-error' : null">{{ old('incident') }}</x-form.textarea>
                    <x-form.error id="incident-error" :message="$errors->first('incident')" />
                </div>
            </div>
            <aside class="resident-form-guidance" aria-labelledby="blotter-help-title">
                <i data-lucide="info" aria-hidden="true"></i>
                <div><h2 id="blotter-help-title">Before you submit</h2><p>Include where the incident happened and who was involved. Staff will review your report before adding it to the official blotter. You can follow updates in <a href="{{ route('account.requests.index') }}">My requests</a>.</p></div>
            </aside>
            <div class="resident-form-actions"><button type="submit" class="resident-button resident-button-primary">Submit blotter report <i data-lucide="arrow-right" aria-hidden="true"></i></button><a href="{{ route('account') }}" class="resident-button resident-button-outline">Cancel</a></div>
        </form>
    </section>
@endsection
