@props(['cancelUrl'])

<div {{ $attributes->class(['form-component-actions']) }}>
    <a href="{{ $cancelUrl }}" class="button button-outline">
        {{ $cancel ?? 'Cancel' }}
    </a>
    <button type="submit" class="button button-primary">
        {{ $submit }}
    </button>
</div>
