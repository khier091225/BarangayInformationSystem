@props(['message' => null])

@if ($message)
    <span {{ $attributes->class(['form-error']) }}>{{ $message }}</span>
@endif
