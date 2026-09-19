@props(['message' => null])

@if ($message)
    <span {{ $attributes->merge(['style' => 'color: #c0392b; font-size: 12px;']) }}>{{ $message }}</span>
@endif
