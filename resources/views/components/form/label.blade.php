@props(['required' => false])

<label {{ $attributes->merge(['style' => 'display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;']) }}>
    {{ $slot }}@if ($required) <span style="color: red;">*</span> @endif
</label>
