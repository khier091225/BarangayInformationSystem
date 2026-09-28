@props(['name'])

<select name="{{ $name }}" {{ $attributes->class(['form-control'])->merge(['id' => $name]) }}>
    {{ $slot }}
</select>
