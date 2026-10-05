@props(['household', 'modal' => false, 'returnToDetails' => false])

@php
    $fieldPrefix = $modal ? 'household-edit-'.$household->getKey().'-' : '';
    $showOldInput = ! $modal || old('_household_edit_id') === (string) $household->getKey();
@endphp

<form method="POST" action="{{ route('households.update', [$household]) }}">
    @csrf
    @method('PUT')
    @if ($modal)
        <input type="hidden" name="_household_edit_id" value="{{ $household->getKey() }}">
    @endif
    @if ($returnToDetails)
        <input type="hidden" name="_return_to" value="households.show">
    @endif

    @if ($modal) <div class="bis-dialog-body"> @endif
    <div class="record-reference">
        <span>Household number</span>
        <strong>{{ $household->household_number }}</strong>
    </div>

    <div class="record-form-field">
        <x-form.label :for="$fieldPrefix.'household_head'" required>Household Head</x-form.label>
        <x-form.input type="text" name="household_head" :id="$fieldPrefix.'household_head'" :value="$showOldInput ? old('household_head', $household->household_head) : $household->household_head" required maxlength="255" :aria-invalid="$showOldInput && $errors->has('household_head') ? 'true' : 'false'" :aria-describedby="$showOldInput && $errors->has('household_head') ? $fieldPrefix.'household_head-error' : null" />
        <x-form.error :message="$showOldInput ? $errors->first('household_head') : null" :id="$fieldPrefix.'household_head-error'" />
    </div>

    <div class="record-form-field">
        <x-form.label :for="$fieldPrefix.'address'" required>Address</x-form.label>
        <x-form.input type="text" name="address" :id="$fieldPrefix.'address'" :value="$showOldInput ? old('address', $household->address) : $household->address" required maxlength="255" :aria-invalid="$showOldInput && $errors->has('address') ? 'true' : 'false'" :aria-describedby="$showOldInput && $errors->has('address') ? $fieldPrefix.'address-error' : null" />
        <x-form.error :message="$showOldInput ? $errors->first('address') : null" :id="$fieldPrefix.'address-error'" />
    </div>

    @if ($modal)
        </div>
        <div class="form-component-actions">
            <button type="button" class="button button-outline" data-household-dialog-close>Cancel</button>
            <button type="submit" class="button button-primary">Save changes</button>
        </div>
    @else
        <x-form.actions :cancel-url="route('households.index')">
            <x-slot:submit>Save changes</x-slot:submit>
        </x-form.actions>
    @endif
</form>
