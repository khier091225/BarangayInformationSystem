@props(['id', 'title', 'description' => null, 'icon' => 'info', 'wide' => false, 'compact' => false, 'openOnLoad' => false])

<dialog id="{{ $id }}" {{ $attributes->class(['bis-dialog', 'bis-dialog--wide' => $wide, 'bis-dialog--compact' => $compact]) }} aria-labelledby="{{ $id }}-title" @if ($description !== null) aria-describedby="{{ $id }}-description" @endif @if ($openOnLoad) data-open-on-load @endif>
    <header class="bis-dialog-header">
        <span class="bis-dialog-icon" aria-hidden="true"><i data-lucide="{{ $icon }}"></i></span>
        <div class="bis-dialog-heading">
            <h2 id="{{ $id }}-title" tabindex="-1" data-dialog-heading>{{ $title }}</h2>
            @if ($description !== null)
                <p id="{{ $id }}-description">{{ $description }}</p>
            @endif
        </div>
        <button type="button" class="bis-dialog-close" data-dialog-close aria-label="Close dialog"><i data-lucide="x" aria-hidden="true"></i></button>
    </header>
    {{ $slot }}
    @isset($footer)
        <div class="bis-dialog-footer">{{ $footer }}</div>
    @endisset
</dialog>
