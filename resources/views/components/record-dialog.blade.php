@props(['id', 'title', 'description', 'icon' => 'plus', 'openOnLoad' => false, 'wide' => false])

<dialog id="{{ $id }}" class="record-dialog{{ $wide ? ' record-dialog--wide' : '' }}" data-record-dialog aria-labelledby="{{ $id }}-title" aria-describedby="{{ $id }}-description" @if ($openOnLoad) data-open-on-load @endif>
    <div class="dialog-top">
        <span class="record-dialog-icon" aria-hidden="true"><i data-lucide="{{ $icon }}"></i></span>
        <button type="button" class="icon-button" data-record-dialog-close aria-label="Close {{ strtolower($title) }} form"><i data-lucide="x" aria-hidden="true"></i></button>
    </div>
    <h2 id="{{ $id }}-title">{{ $title }}</h2>
    <p id="{{ $id }}-description">{{ $description }}</p>
    {{ $slot }}
</dialog>
