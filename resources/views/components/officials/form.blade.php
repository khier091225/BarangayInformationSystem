@props(['official' => null, 'modal' => false])

@php
    $editing = $official !== null;
    $formContext = $editing ? 'officials.edit.'.$official->getKey() : 'officials.create';
    $useOld = ! $modal || old('_record_form') === $formContext;
    $value = fn (string $field, mixed $default = null): mixed => $useOld ? old($field, $default) : $default;
    $error = fn (string $field): ?string => $useOld ? $errors->first($field) : null;
    $fieldId = fn (string $field): string => $modal ? str_replace('.', '-', $formContext).'-'.$field : $field;
@endphp

<form method="POST" action="{{ $editing ? route('officials.update', [$official]) : route('officials.store') }}" enctype="multipart/form-data">
    @csrf
    @if ($editing) @method('PUT') @endif
    @if ($modal) <input type="hidden" name="_record_form" value="{{ $formContext }}"> @endif

    @if ($modal) <div class="bis-dialog-body"> @endif
    <div class="record-form-field">
        <x-form.label :for="$fieldId('name')" required>Full Name</x-form.label>
        <x-form.input name="name" :id="$fieldId('name')" :value="$value('name', $official?->name)" required maxlength="255" placeholder="e.g. Hon. Juan Dela Cruz" :aria-invalid="$error('name') ? 'true' : 'false'" :aria-describedby="$error('name') ? $fieldId('name').'-error' : null" />
        <x-form.error :message="$error('name')" :id="$fieldId('name').'-error'" />
    </div>

    <div class="record-form-grid">
        <div>
            <x-form.label :for="$fieldId('position')" required>Position</x-form.label>
            <x-form.select name="position" :id="$fieldId('position')" required :aria-invalid="$error('position') ? 'true' : 'false'" :aria-describedby="$error('position') ? $fieldId('position').'-error' : null">
                <option value="">Select a position</option>
                @foreach (['Barangay Captain' => 'Barangay Captain (Punong Barangay)', 'Barangay Kagawad' => 'Barangay Kagawad (Councilor)', 'SK Chairman' => 'SK Chairman', 'Barangay Secretary' => 'Barangay Secretary', 'Barangay Treasurer' => 'Barangay Treasurer'] as $position => $label)
                    <option value="{{ $position }}" @selected($value('position', $official?->position) === $position)>{{ $label }}</option>
                @endforeach
            </x-form.select>
            <x-form.error :message="$error('position')" :id="$fieldId('position').'-error'" />
        </div>
        <div>
            <x-form.label :for="$fieldId('contact_number')">Contact Number</x-form.label>
            <x-form.input name="contact_number" :id="$fieldId('contact_number')" :value="$value('contact_number', $official?->contact_number)" maxlength="50" placeholder="e.g. 09171234567" :aria-invalid="$error('contact_number') ? 'true' : 'false'" :aria-describedby="$error('contact_number') ? $fieldId('contact_number').'-error' : null" />
            <x-form.error :message="$error('contact_number')" :id="$fieldId('contact_number').'-error'" />
        </div>
    </div>

    <div class="record-form-grid">
        <div>
            <x-form.label :for="$fieldId('term_start')">Term Start Date</x-form.label>
            <x-form.input type="date" name="term_start" :id="$fieldId('term_start')" :value="$value('term_start', $official?->term_start?->format('Y-m-d'))" :aria-invalid="$error('term_start') ? 'true' : 'false'" :aria-describedby="$error('term_start') ? $fieldId('term_start').'-error' : null" />
            <x-form.error :message="$error('term_start')" :id="$fieldId('term_start').'-error'" />
        </div>
        <div>
            <x-form.label :for="$fieldId('term_end')">Term End Date</x-form.label>
            <x-form.input type="date" name="term_end" :id="$fieldId('term_end')" :value="$value('term_end', $official?->term_end?->format('Y-m-d'))" :aria-invalid="$error('term_end') ? 'true' : 'false'" :aria-describedby="$error('term_end') ? $fieldId('term_end').'-error' : null" />
            <x-form.error :message="$error('term_end')" :id="$fieldId('term_end').'-error'" />
        </div>
    </div>

    <div class="record-form-field">
        <x-form.label :for="$fieldId('photo')">Official photo</x-form.label>
        @if ($editing && $official->image_path)
            <div class="official-photo-preview">
                <img src="{{ asset('storage/'.$official->image_path) }}" alt="Current photo of {{ $official->name }}" width="56" height="56">
                <label for="{{ $fieldId('remove_photo') }}"><input type="checkbox" name="remove_photo" id="{{ $fieldId('remove_photo') }}" value="1" aria-invalid="{{ $error('remove_photo') ? 'true' : 'false' }}" @if ($error('remove_photo')) aria-describedby="{{ $fieldId('remove_photo') }}-error" @endif @checked($value('remove_photo', false))> Remove current photo</label>
            </div>
        @endif
        <x-form.input type="file" name="photo" :id="$fieldId('photo')" accept="image/jpeg,image/png,image/webp" :aria-invalid="$error('photo') ? 'true' : 'false'" :aria-describedby="$fieldId('photo').'-help'.($error('photo') ? ' '.$fieldId('photo').'-error' : '')" />
        <p id="{{ $fieldId('photo') }}-help" class="official-photo-help">Optional. JPG, PNG, or WebP, up to 2 MB. This photo appears on the public homepage.</p>
        <x-form.error :message="$error('photo')" :id="$fieldId('photo').'-error'" />
        <x-form.error :message="$error('remove_photo')" :id="$fieldId('remove_photo').'-error'" />
    </div>

    @if ($modal)
        </div>
        <div class="form-component-actions">
            <button type="button" class="button button-outline" data-record-dialog-close>Cancel</button>
            <button type="submit" class="button button-primary">{{ $editing ? 'Save changes' : 'Save official' }}</button>
        </div>
    @else
        <x-form.actions :cancel-url="route('officials.index')">
            <x-slot:submit>{{ $editing ? 'Save changes' : 'Save official' }}</x-slot:submit>
        </x-form.actions>
    @endif
</form>
