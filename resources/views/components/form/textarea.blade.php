@props(['name'])

<textarea name="{{ $name }}" {{ $attributes->class(['form-control'])->merge(['id' => $name]) }}>{{ $slot }}</textarea>
