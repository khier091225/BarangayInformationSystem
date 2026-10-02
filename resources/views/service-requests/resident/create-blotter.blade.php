@extends('layouts.resident')

@section('title', 'File a Blotter Report | Barangay Information System')
@section('main-class', 'resident-main-form')

@section('content')
    <x-workspace.page-header title="File a blotter report" description="Describe the incident for barangay staff to review before it becomes an official blotter record." icon="notebook-pen">
        <x-slot:actions>
            <a href="{{ route('account.requests.index', ['type' => 'blotter']) }}" class="resident-button resident-button-primary"><i data-lucide="inbox" aria-hidden="true"></i> My blotter requests</a>
        </x-slot:actions>
    </x-workspace.page-header>

    <section class="resident-card resident-form-card resident-service-form" aria-label="Blotter report form">
        <div class="resident-form-intro">
            <span class="resident-section-icon"><i data-lucide="user-round" aria-hidden="true"></i></span>
            <div><span>Complainant</span><strong>{{ auth()->user()->name }}</strong></div>
            <p>Fields marked * are required.</p>
        </div>
        <form method="POST" action="{{ route('account.requests.blotter.store') }}" class="resident-form resident-service-grid">
            @csrf
            <div class="resident-form-fields">
                <div class="resident-field resident-field-wide">
                    <x-form.label for="incident" required>What happened?</x-form.label>
                    <x-form.textarea name="incident" rows="5" required minlength="10" maxlength="5000" placeholder="Describe what happened and where. Include names if you know them." :aria-invalid="$errors->has('incident') ? 'true' : 'false'" :aria-describedby="$errors->has('incident') ? 'incident-error' : null">{{ old('incident') }}</x-form.textarea>
                    <x-form.error id="incident-error" :message="$errors->first('incident')" />
                </div>
            </div>
            <details class="complaint-optional-fields" @if ($errors->has('respondent') || $errors->has('incident_date')) open @endif>
                <summary>Add person involved or incident date <span>(optional)</span></summary>
                <div class="complaint-optional-body">
                    <div class="resident-field">
                        <x-form.label for="respondent">Person involved</x-form.label>
                        <x-form.input name="respondent" :value="old('respondent')" maxlength="255" placeholder="Leave blank if you don't know their name" :aria-invalid="$errors->has('respondent') ? 'true' : 'false'" :aria-describedby="$errors->has('respondent') ? 'respondent-error' : null" />
                        <x-form.error id="respondent-error" :message="$errors->first('respondent')" />
                    </div>
                    <div class="resident-field">
                        <x-form.label for="incident_date">Date of incident</x-form.label>
                        <x-form.input name="incident_date" type="date" :value="old('incident_date')" max="{{ today('Asia/Manila')->toDateString() }}" :aria-invalid="$errors->has('incident_date') ? 'true' : 'false'" :aria-describedby="$errors->has('incident_date') ? 'blotter-date-help incident-date-error' : 'blotter-date-help'" />
                        <p id="blotter-date-help" class="incident-field-help">Leave blank if it happened today.</p>
                        <x-form.error id="incident-date-error" :message="$errors->first('incident_date')" />
                    </div>
                </div>
            </details>
            <aside class="resident-form-guidance" aria-labelledby="blotter-help-title">
                <i data-lucide="info" aria-hidden="true"></i>
                <div><h2 id="blotter-help-title">Before you submit</h2><p>Include the place and what happened. You can add the person's name later through barangay staff if you do not know it now. Follow updates in <a href="{{ route('account.requests.index', ['type' => 'blotter']) }}">My blotter requests</a>.</p></div>
            </aside>
            <div class="resident-form-actions"><button type="submit" class="resident-button resident-button-primary">Submit blotter report <i data-lucide="arrow-right" aria-hidden="true"></i></button><a href="{{ route('account') }}" class="resident-button resident-button-outline">Cancel</a></div>
        </form>
    </section>
@endsection
