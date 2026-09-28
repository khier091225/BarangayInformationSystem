@props(['name', 'type' => 'text'])

<input type="{{ $type }}" name="{{ $name }}" {{ $attributes->class(['form-control'])->merge(['id' => $name]) }}>
