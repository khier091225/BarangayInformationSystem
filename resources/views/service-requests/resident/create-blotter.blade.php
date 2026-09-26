@extends('layouts.resident')

@section('title', 'File a Blotter Report | Barangay Information System')

@section('content')
    <a href="{{ route('account') }}" class="resident-page-back"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to dashboard</a>
    <div class="resident-page-heading">
        <div>
            <span class="resident-kicker resident-kicker-dark">INCIDENT REPORT</span>
            <h1>File a blotter report</h1>
            <p>Share the incident details below. Barangay staff will review your report before recording it in the official blotter.</p>
        </div>
    </div>

    <div class="resident-form-layout">
        <section class="resident-card resident-form-card" aria-label="Blotter report form">
            <div class="resident-form-intro"><span class="resident-section-icon"><i data-lucide="user-round" aria-hidden="true"></i></span><span>Complainant <strong>{{ auth()->user()->name }}</strong></span></div>
            <form method="POST" action="{{ route('account.requests.blotter.store') }}" class="resident-form">
                @csrf
                <div class="resident-field">
                    <x-form.label for="respondent" required>Respondent name</x-form.label>
                    <x-form.input name="respondent" :value="old('respondent')" required maxlength="255" placeholder="Name of the person involved" />
                    <x-form.error :message="$errors->first('respondent')" />
                </div>
                <div class="resident-field">
                    <x-form.label for="incident_date" required>Date of incident</x-form.label>
                    <x-form.input name="incident_date" type="date" :value="old('incident_date')" max="{{ today()->toDateString() }}" required />
                    <x-form.error :message="$errors->first('incident_date')" />
                </div>
                <div class="resident-field">
                    <x-form.label for="incident" required>What happened?</x-form.label>
                    <x-form.textarea name="incident" rows="6" required minlength="10" maxlength="5000" placeholder="Describe the incident, place, and people involved.">{{ old('incident') }}</x-form.textarea>
                    <x-form.error :message="$errors->first('incident')" />
                </div>
                <button type="submit" class="resident-button resident-button-primary">Submit blotter report <i data-lucide="arrow-right" aria-hidden="true"></i></button>
            </form>
        </section>
        <aside class="resident-card resident-form-help" aria-labelledby="blotter-help-title">
            <span class="resident-section-icon resident-section-icon-warm"><i data-lucide="info" aria-hidden="true"></i></span>
            <h2 id="blotter-help-title">Before you submit</h2>
            <p>Include clear details so barangay staff can review the incident accurately.</p>
            <ul>
                <li>Provide the respondent's name and the incident date.</li>
                <li>Describe what happened and where it took place.</li>
                <li>Track staff updates in My requests after submission.</li>
            </ul>
        </aside>
    </div>
@endsection
