<div>
    <!-- Happiness is not something readymade. It comes from your own actions. - Dalai Lama -->
</div>
@extends('layouts.resident')

@section('title', 'File a Blotter Report | Barangay Information System')

@section('content')
    <a href="{{ route('account') }}" class="resident-muted">← Back to dashboard</a>
    <div style="margin: 18px 0 24px;">
        <div class="eyebrow">INCIDENT REPORT</div>
        <h1 class="resident-heading">File a blotter report</h1>
        <p class="resident-muted">Provide accurate details. Staff will review the report before adding it to the official blotter records.</p>
    </div>

    <div class="resident-card" style="max-width: 700px;">
        <p class="resident-muted" style="margin-top: 0;">Complainant: <strong>{{ auth()->user()->name }}</strong></p>
        <form method="POST" action="{{ route('account.requests.blotter.store') }}">
            @csrf
            <div style="margin-bottom: 18px;">
                <x-form.label for="respondent" required>Respondent name</x-form.label>
                <x-form.input name="respondent" :value="old('respondent')" required maxlength="255" placeholder="Name of the person involved" />
                <x-form.error :message="$errors->first('respondent')" />
            </div>
            <div style="margin-bottom: 18px;">
                <x-form.label for="incident_date" required>Date of incident</x-form.label>
                <x-form.input name="incident_date" type="date" :value="old('incident_date')" max="{{ today()->toDateString() }}" required />
                <x-form.error :message="$errors->first('incident_date')" />
            </div>
            <div style="margin-bottom: 24px;">
                <x-form.label for="incident" required>What happened?</x-form.label>
                <x-form.textarea name="incident" rows="6" required minlength="10" maxlength="5000" placeholder="Describe the incident, place, and people involved.">{{ old('incident') }}</x-form.textarea>
                <x-form.error :message="$errors->first('incident')" />
            </div>
            <button type="submit" class="button button-primary">Submit blotter report</button>
        </form>
    </div>
@endsection
