@props(['modal' => false])

@php
    $useOld = ! $modal || old('_record_form') === 'blotters.create';
    $value = fn (string $field): mixed => $useOld ? old($field) : null;
    $error = fn (string $field): ?string => $useOld ? $errors->first($field) : null;
    $fieldId = fn (string $field): string => $modal ? 'blotters-create-'.$field : $field;
@endphp

<form method="POST" action="{{ route('blotters.store') }}" class="complaint-staff-form">
    @csrf
    @if ($modal) <input type="hidden" name="_record_form" value="blotters.create"> @endif
    <div class="resident-field">
        <x-form.label :for="$fieldId('complainant')" required>Complainant name</x-form.label>
        <x-form.input name="complainant" :id="$fieldId('complainant')" :value="$value('complainant')" required maxlength="255" placeholder="Full name of complainant" :aria-invalid="$error('complainant') ? 'true' : 'false'" :aria-describedby="$error('complainant') ? $fieldId('complainant').'-error' : null" />
        <x-form.error :message="$error('complainant')" :id="$fieldId('complainant').'-error'" />
    </div>
    <div class="resident-field">
        <x-form.label :for="$fieldId('incident')" required>What happened?</x-form.label>
        <x-form.textarea name="incident" :id="$fieldId('incident')" rows="5" required placeholder="Describe the incident and where it happened." :aria-invalid="$error('incident') ? 'true' : 'false'" :aria-describedby="$error('incident') ? $fieldId('incident').'-error' : null">{{ $value('incident') }}</x-form.textarea>
        <x-form.error :message="$error('incident')" :id="$fieldId('incident').'-error'" />
    </div>
    <details class="complaint-optional-fields" @if ($error('respondent') || $error('incident_date')) open @endif>
        <summary>Add respondent or incident date <span>(optional)</span></summary>
        <div class="complaint-optional-body">
            <div class="resident-field">
                <x-form.label :for="$fieldId('respondent')">Respondent name</x-form.label>
                <x-form.input name="respondent" :id="$fieldId('respondent')" :value="$value('respondent')" maxlength="255" placeholder="Leave blank if unknown" :aria-invalid="$error('respondent') ? 'true' : 'false'" :aria-describedby="$error('respondent') ? $fieldId('respondent').'-error' : null" />
                <x-form.error :message="$error('respondent')" :id="$fieldId('respondent').'-error'" />
            </div>
            <div class="resident-field">
                <x-form.label :for="$fieldId('incident_date')">Date of incident</x-form.label>
                <x-form.input name="incident_date" :id="$fieldId('incident_date')" type="date" :value="$value('incident_date')" :aria-invalid="$error('incident_date') ? 'true' : 'false'" :aria-describedby="$fieldId('incident_date').'-help'.($error('incident_date') ? ' '.$fieldId('incident_date').'-error' : '')" />
                <p id="{{ $fieldId('incident_date') }}-help" class="incident-field-help">Leave blank if it happened today.</p>
                <x-form.error :message="$error('incident_date')" :id="$fieldId('incident_date').'-error'" />
            </div>
        </div>
    </details>
    @if ($modal)
        <div class="form-component-actions">
            <button type="button" class="button button-outline" data-record-dialog-close>Cancel</button>
            <button type="submit" class="button button-primary">Save blotter record</button>
        </div>
    @else
        <x-form.actions :cancel-url="route('blotters.index')">
            <x-slot:submit>Save blotter record</x-slot:submit>
        </x-form.actions>
    @endif
</form>
