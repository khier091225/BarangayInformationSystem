@props(['label', 'surface' => true])

<div {{ $attributes->class(['table-scroll-panel', 'table-scroll-panel-surface' => $surface]) }} data-table-scroll-panel>
    @isset($heading)
        {{ $heading }}
    @endisset

    <p class="table-scroll-hint" data-table-scroll-hint hidden>
        <i data-lucide="arrow-left-right" aria-hidden="true"></i>
        <span>Swipe left or right to see more columns.<span class="sr-only"> Keyboard users can focus this table and use the left and right arrow keys.</span></span>
    </p>

    <div class="table-scroll" data-table-scroll role="region" aria-label="{{ $label }}" tabindex="0">
        {{ $slot }}
    </div>
</div>
