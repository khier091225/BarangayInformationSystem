@props(['modal' => false])

@php($showOldInput = ! old('_household_edit_id'))

<form method="POST" action="{{ route('households.store') }}">
    @csrf

    <div style="margin-bottom: 18px;">
        <x-form.label for="household_head" required>Household Head</x-form.label>
        <x-form.input type="text" name="household_head" :value="$showOldInput ? old('household_head') : null" required maxlength="255" placeholder="Full name of household head" :aria-invalid="$showOldInput && $errors->has('household_head') ? 'true' : 'false'" :aria-describedby="$showOldInput && $errors->has('household_head') ? 'household_head-error' : null" />
        <x-form.error :message="$showOldInput ? $errors->first('household_head') : null" :id="'household_head-error'" />
    </div>

    <div style="margin-bottom: 24px;">
        <x-form.label for="address" required>Address</x-form.label>
        <x-form.input type="text" name="address" :value="$showOldInput ? old('address') : null" required maxlength="255" placeholder="e.g. 124 Rizal St., Purok 3" :aria-invalid="$showOldInput && $errors->has('address') ? 'true' : 'false'" :aria-describedby="$showOldInput && $errors->has('address') ? 'address-error' : null" />
        <x-form.error :message="$showOldInput ? $errors->first('address') : null" :id="'address-error'" />
    </div>

    @if ($modal)
        <div class="form-component-actions">
            <button type="button" class="button button-outline" data-household-dialog-close>Cancel</button>
            <button type="submit" class="button button-primary">Save Household</button>
        </div>
    @else
        <x-form.actions :cancel-url="route('households.index')">
            <x-slot:submit>Save Household</x-slot:submit>
        </x-form.actions>
    @endif
</form>
