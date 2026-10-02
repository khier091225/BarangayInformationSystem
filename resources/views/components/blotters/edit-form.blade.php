@props(['blotter', 'modal' => false])

@php
    $formContext = 'blotters.edit.'.$blotter->getKey();
    $useOld = ! $modal || old('_record_form') === $formContext;
    $value = fn (string $field, mixed $default = null): mixed => $useOld ? old($field, $default) : $default;
    $error = fn (string $field): ?string => $useOld ? $errors->first($field) : null;
    $fieldId = fn (string $field): string => $modal ? str_replace('.', '-', $formContext).'-'.$field : $field;
@endphp

<form method="POST" action="{{ route('blotters.update', [$blotter]) }}">
    @csrf
    @method('PUT')
    @if ($modal) <input type="hidden" name="_record_form" value="{{ $formContext }}"> @endif

    <div class="record-form-grid">
        <div>
            <x-form.label :for="$fieldId('complainant')" required>Complainant Name</x-form.label>
            <x-form.input name="complainant" :id="$fieldId('complainant')" :value="$value('complainant', $blotter->complainant)" required maxlength="255" :aria-invalid="$error('complainant') ? 'true' : 'false'" />
            <x-form.error :message="$error('complainant')" />
        </div>
        <div>
            <x-form.label :for="$fieldId('respondent')" required>Respondent Name</x-form.label>
            <x-form.input name="respondent" :id="$fieldId('respondent')" :value="$value('respondent', $blotter->respondent)" required maxlength="255" :aria-invalid="$error('respondent') ? 'true' : 'false'" />
            <x-form.error :message="$error('respondent')" />
        </div>
    </div>

    <div class="record-form-field">
        <x-form.label :for="$fieldId('incident_date')" required>Incident Date</x-form.label>
        <x-form.input type="date" name="incident_date" :id="$fieldId('incident_date')" :value="$value('incident_date', $blotter->incident_date?->format('Y-m-d'))" required :aria-invalid="$error('incident_date') ? 'true' : 'false'" />
        <x-form.error :message="$error('incident_date')" />
    </div>

    <div class="record-form-field">
        <x-form.label :for="$fieldId('incident')" required>Incident Details</x-form.label>
        <x-form.textarea name="incident" :id="$fieldId('incident')" rows="5" required :aria-invalid="$error('incident') ? 'true' : 'false'">{{ $value('incident', $blotter->incident) }}</x-form.textarea>
        <x-form.error :message="$error('incident')" />
    </div>

    @if ($modal)
        <div class="form-component-actions">
            <button type="button" class="button button-outline" data-record-dialog-close>Cancel</button>
            <button type="submit" class="button button-primary">Update blotter record</button>
        </div>
    @else
        <x-form.actions :cancel-url="route('blotters.index')">
            <x-slot:submit>Update blotter record</x-slot:submit>
        </x-form.actions>
    @endif
</form>
