@props(['resident' => null, 'households' => [], 'modal' => false, 'returnToProfile' => false])

@php
    $editing = $resident !== null;
    $formContext = $editing ? 'residents.edit.'.$resident->getKey() : 'residents.create';
    $useOld = ! $modal || old('_record_form') === $formContext;
    $value = fn (string $field, mixed $default = null): mixed => $useOld ? old($field, $default) : $default;
    $error = fn (string $field): ?string => $useOld ? $errors->first($field) : null;
    $fieldId = fn (string $field): string => $modal ? str_replace('.', '-', $formContext).'-'.$field : $field;
@endphp

<form method="POST" action="{{ $editing ? route('residents.update', [$resident]) : route('residents.store') }}">
    @csrf
    @if ($editing) @method('PUT') @endif
    @if ($modal) <input type="hidden" name="_record_form" value="{{ $formContext }}"> @endif
    @if ($editing && $returnToProfile) <input type="hidden" name="_return_to" value="residents.show"> @endif

    @if ($modal) <div class="bis-dialog-body"> @endif
    <div class="record-form-grid record-form-grid--three">
        <div>
            <x-form.label :for="$fieldId('first_name')" required>First Name</x-form.label>
            <x-form.input name="first_name" :id="$fieldId('first_name')" :value="$value('first_name', $resident?->first_name)" required maxlength="255" placeholder="e.g. Juan" :aria-invalid="$error('first_name') ? 'true' : 'false'" :aria-describedby="$error('first_name') ? $fieldId('first_name').'-error' : null" />
            <x-form.error :message="$error('first_name')" :id="$fieldId('first_name').'-error'" />
        </div>
        <div>
            <x-form.label :for="$fieldId('middle_name')">Middle Name</x-form.label>
            <x-form.input name="middle_name" :id="$fieldId('middle_name')" :value="$value('middle_name', $resident?->middle_name)" maxlength="255" placeholder="e.g. Santos" :aria-invalid="$error('middle_name') ? 'true' : 'false'" :aria-describedby="$error('middle_name') ? $fieldId('middle_name').'-error' : null" />
            <x-form.error :message="$error('middle_name')" :id="$fieldId('middle_name').'-error'" />
        </div>
        <div>
            <x-form.label :for="$fieldId('last_name')" required>Last Name</x-form.label>
            <x-form.input name="last_name" :id="$fieldId('last_name')" :value="$value('last_name', $resident?->last_name)" required maxlength="255" placeholder="e.g. Dela Cruz" :aria-invalid="$error('last_name') ? 'true' : 'false'" :aria-describedby="$error('last_name') ? $fieldId('last_name').'-error' : null" />
            <x-form.error :message="$error('last_name')" :id="$fieldId('last_name').'-error'" />
        </div>
    </div>

    <div class="record-form-grid">
        <div>
            <x-form.label :for="$fieldId('household_id')">Household (Optional)</x-form.label>
            <x-form.select name="household_id" :id="$fieldId('household_id')" :aria-invalid="$error('household_id') ? 'true' : 'false'" :aria-describedby="$error('household_id') ? $fieldId('household_id').'-error' : null">
                <option value="">No household</option>
                @foreach ($households as $household)
                    <option value="{{ $household->id }}" @selected((string) $value('household_id', $resident?->household_id) === (string) $household->id)>{{ $household->household_number }} (Head: {{ $household->household_head }})</option>
                @endforeach
            </x-form.select>
            <x-form.error :message="$error('household_id')" :id="$fieldId('household_id').'-error'" />
        </div>
        <div>
            <x-form.label :for="$fieldId('birthdate')" required>Birthdate</x-form.label>
            <x-form.input type="date" name="birthdate" :id="$fieldId('birthdate')" :value="$value('birthdate', $resident?->birthdate?->format('Y-m-d'))" required :max="date('Y-m-d')" :aria-invalid="$error('birthdate') ? 'true' : 'false'" :aria-describedby="$error('birthdate') ? $fieldId('birthdate').'-error' : null" />
            <x-form.error :message="$error('birthdate')" :id="$fieldId('birthdate').'-error'" />
        </div>
    </div>

    <div class="record-form-grid">
        <div>
            <x-form.label :for="$fieldId('gender')" required>Gender</x-form.label>
            <x-form.select name="gender" :id="$fieldId('gender')" required :aria-invalid="$error('gender') ? 'true' : 'false'" :aria-describedby="$error('gender') ? $fieldId('gender').'-error' : null">
                <option value="">Select gender</option>
                @foreach (['Male', 'Female'] as $gender)
                    <option value="{{ $gender }}" @selected($value('gender', $resident?->gender) === $gender)>{{ $gender }}</option>
                @endforeach
            </x-form.select>
            <x-form.error :message="$error('gender')" :id="$fieldId('gender').'-error'" />
        </div>
        <div>
            <x-form.label :for="$fieldId('civil_status')" required>Civil Status</x-form.label>
            <x-form.select name="civil_status" :id="$fieldId('civil_status')" required :aria-invalid="$error('civil_status') ? 'true' : 'false'" :aria-describedby="$error('civil_status') ? $fieldId('civil_status').'-error' : null">
                <option value="">Select civil status</option>
                @foreach (['Single', 'Married', 'Widowed', 'Separated', 'Divorced'] as $civilStatus)
                    <option value="{{ $civilStatus }}" @selected($value('civil_status', $resident?->civil_status) === $civilStatus)>{{ $civilStatus }}</option>
                @endforeach
            </x-form.select>
            <x-form.error :message="$error('civil_status')" :id="$fieldId('civil_status').'-error'" />
        </div>
    </div>

    <div class="record-form-grid">
        <div>
            <x-form.label :for="$fieldId('address')" required>Address</x-form.label>
            <x-form.input name="address" :id="$fieldId('address')" :value="$value('address', $resident?->address)" required maxlength="255" placeholder="e.g. Purok 4, Ilang-Ilang St." :aria-invalid="$error('address') ? 'true' : 'false'" :aria-describedby="$error('address') ? $fieldId('address').'-error' : null" />
            <x-form.error :message="$error('address')" :id="$fieldId('address').'-error'" />
        </div>
        <div>
            <x-form.label :for="$fieldId('contact_number')">Contact Number</x-form.label>
            <x-form.input name="contact_number" :id="$fieldId('contact_number')" :value="$value('contact_number', $resident?->contact_number)" maxlength="50" placeholder="e.g. 09171234567" :aria-invalid="$error('contact_number') ? 'true' : 'false'" :aria-describedby="$error('contact_number') ? $fieldId('contact_number').'-error' : null" />
            <x-form.error :message="$error('contact_number')" :id="$fieldId('contact_number').'-error'" />
        </div>
    </div>

    <div class="record-voter-field">
        <input type="hidden" name="is_voter" value="0">
        <label>
            <input type="checkbox" name="is_voter" id="{{ $fieldId('is_voter') }}" value="1" aria-invalid="{{ $error('is_voter') ? 'true' : 'false' }}" @if ($error('is_voter')) aria-describedby="{{ $fieldId('is_voter') }}-error" @endif @checked((bool) $value('is_voter', $resident?->is_voter ?? false))>
            <span>Registered voter</span>
        </label>
        <x-form.error :message="$error('is_voter')" :id="$fieldId('is_voter').'-error'" />
    </div>

    @if ($modal)
        </div>
        <div class="form-component-actions">
            <button type="button" class="button button-outline" data-record-dialog-close>Cancel</button>
            <button type="submit" class="button button-primary">{{ $editing ? 'Save changes' : 'Save resident' }}</button>
        </div>
    @else
        <x-form.actions :cancel-url="route('residents.index')">
            <x-slot:submit>{{ $editing ? 'Save changes' : 'Save resident' }}</x-slot:submit>
        </x-form.actions>
    @endif
</form>
