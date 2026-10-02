@props(['residents', 'certificateFees', 'selectedResidentId' => null, 'modal' => false])

@php
    $useOld = ! $modal || old('_record_form') === 'certificates.create';
    $value = fn (string $field, mixed $default = null): mixed => $useOld ? old($field, $default) : $default;
    $error = fn (string $field): ?string => $useOld ? $errors->first($field) : null;
    $fieldId = fn (string $field): string => $modal ? 'certificates-create-'.$field : $field;
@endphp

<form method="POST" action="{{ route('certificates.store') }}">
    @csrf
    @if ($modal) <input type="hidden" name="_record_form" value="certificates.create"> @endif

    <div class="record-form-field">
        <x-form.label :for="$fieldId('resident_id')" required>Resident</x-form.label>
        <x-form.select name="resident_id" :id="$fieldId('resident_id')" required :aria-invalid="$error('resident_id') ? 'true' : 'false'">
            <option value="">Choose a resident</option>
            @foreach ($residents as $resident)
                <option value="{{ $resident->id }}" @selected((string) $value('resident_id', $selectedResidentId) === (string) $resident->id)>{{ $resident->last_name }}, {{ $resident->first_name }} {{ $resident->middle_name }} ({{ $resident->address }})</option>
            @endforeach
        </x-form.select>
        <x-form.error :message="$error('resident_id')" />
    </div>

    <div class="record-form-grid">
        <div>
            <x-form.label :for="$fieldId('certificate_type')" required>Document type</x-form.label>
            <x-form.select name="certificate_type" :id="$fieldId('certificate_type')" required :aria-invalid="$error('certificate_type') ? 'true' : 'false'">
                <option value="">Select type</option>
                @foreach ($certificateFees as $type => $fee)
                    <option value="{{ $type }}" @selected($value('certificate_type') === $type)>{{ $type }} — {{ (float) $fee === 0.0 ? 'Free' : '₱'.number_format((float) $fee, 2) }}</option>
                @endforeach
            </x-form.select>
            <x-form.error :message="$error('certificate_type')" />
        </div>
        <div>
            <x-form.label :for="$fieldId('date_issued')" required>Date Issued</x-form.label>
            <x-form.input type="date" name="date_issued" :id="$fieldId('date_issued')" :value="$value('date_issued', date('Y-m-d'))" required :aria-invalid="$error('date_issued') ? 'true' : 'false'" />
            <x-form.error :message="$error('date_issued')" />
        </div>
    </div>

    <div class="record-form-field">
        <x-form.label :for="$fieldId('purpose')" required>Purpose</x-form.label>
        <x-form.input name="purpose" :id="$fieldId('purpose')" :value="$value('purpose')" required maxlength="255" placeholder="e.g. Local Employment, Scholarship Application, Bank Account Requirement" :aria-invalid="$error('purpose') ? 'true' : 'false'" />
        <x-form.error :message="$error('purpose')" />
    </div>

    @if ($modal)
        <div class="form-component-actions">
            <button type="button" class="button button-outline" data-record-dialog-close>Cancel</button>
            <button type="submit" class="button button-primary">Issue certificate</button>
        </div>
    @else
        <x-form.actions :cancel-url="route('certificates.index')">
            <x-slot:submit>Issue certificate</x-slot:submit>
        </x-form.actions>
    @endif
</form>
