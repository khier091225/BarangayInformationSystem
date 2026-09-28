@props(['label', 'value', 'icon', 'href', 'tone' => 'green'])

<a href="{{ $href }}" {{ $attributes->class(['overview-stat', 'overview-stat-'.$tone]) }}>
    <span class="overview-stat-icon"><i data-lucide="{{ $icon }}" aria-hidden="true"></i></span>
    <span class="overview-stat-label">{{ $label }}</span>
    <strong>{{ number_format($value) }}</strong>
</a>
