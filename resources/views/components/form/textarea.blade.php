@props(['name'])

<textarea name="{{ $name }}" {{ $attributes->merge(['id' => $name, 'style' => 'width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box; line-height: 1.6;']) }}>{{ $slot }}</textarea>
