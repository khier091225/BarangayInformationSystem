@props(['cancelUrl'])

<div {{ $attributes->merge(['style' => 'display: flex; justify-content: flex-end; gap: 12px;']) }}>
    <a href="{{ $cancelUrl }}" class="button" style="text-decoration: none; padding: 10px 18px; border: 1px solid #ccd5c8; border-radius: 6px; color: #555;">
        {{ $cancel ?? 'Cancel' }}
    </a>
    <button type="submit" class="button button-primary" style="padding: 10px 22px; cursor: pointer;">
        {{ $submit }}
    </button>
</div>
