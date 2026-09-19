@props(['name'])

<select name="{{ $name }}" {{ $attributes->merge(['id' => $name, 'style' => 'width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; background: white; box-sizing: border-box;']) }}>
    {{ $slot }}
</select>
