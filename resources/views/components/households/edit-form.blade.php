@props(['household', 'modal' => false])

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

    <div style="margin-bottom: 18px;">
        <span style="display: block; margin-bottom: 6px; color: var(--muted); font-size: 13px; font-weight: 600;">Household Number</span>
        <strong style="display: block; color: var(--ink); font-size: 15px;">{{ $household->household_number }}</strong>
        <small style="display: block; margin-top: 4px; color: var(--muted-soft);">Assigned by the system and cannot be changed.</small>
    </div>

    <div style="margin-bottom: 18px;">
        <x-form.label :for="$fieldPrefix.'household_head'" required>Household Head</x-form.label>
        <x-form.input type="text" name="household_head" :id="$fieldPrefix.'household_head'" :value="$showOldInput ? old('household_head', $household->household_head) : $household->household_head" required maxlength="255" :aria-invalid="$showOldInput && $errors->has('household_head') ? 'true' : 'false'" />
        <x-form.error :message="$showOldInput ? $errors->first('household_head') : null" />
    </div>

    <div style="margin-bottom: 24px;">
        <x-form.label :for="$fieldPrefix.'address'" required>Address</x-form.label>
        <x-form.input type="text" name="address" :id="$fieldPrefix.'address'" :value="$showOldInput ? old('address', $household->address) : $household->address" required maxlength="255" :aria-invalid="$showOldInput && $errors->has('address') ? 'true' : 'false'" />
        <x-form.error :message="$showOldInput ? $errors->first('address') : null" />
    </div>

    @if ($modal)
        <div class="form-component-actions">
            <button type="button" class="button button-outline" data-household-dialog-close>Cancel</button>
            <button type="submit" class="button button-primary">Update Household</button>
        </div>
    @else
        <x-form.actions :cancel-url="route('households.index')">
            <x-slot:submit>Update Household</x-slot:submit>
        </x-form.actions>
    @endif
</form>
