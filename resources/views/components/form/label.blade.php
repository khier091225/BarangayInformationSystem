@props(['required' => false])

<label {{ $attributes->class(['form-label']) }}>
    {{ $slot }}@if ($required) <span class="form-required" aria-hidden="true">*</span> @endif
</label>
