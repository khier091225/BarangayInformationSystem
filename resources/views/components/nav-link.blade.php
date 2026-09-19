@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}" {{ $attributes->class(['selected' => $active])->merge(['aria-current' => $active ? 'page' : null]) }}><i data-lucide="{{ $icon }}"></i> {{ $slot }}</a>
