@props([
    'title',
    'description' => null,
    'icon',
])

<header {{ $attributes->class(['workspace-page-header']) }}>
    <div class="workspace-page-header-intro">
        <span class="workspace-page-header-icon"><i data-lucide="{{ $icon }}" aria-hidden="true"></i></span>
        <div class="workspace-page-header-copy">
            <div class="workspace-page-header-title">
                <h1>{{ $title }}</h1>
                @isset($status)
                    {{ $status }}
                @endisset
            </div>
            @if (filled($description))
                <p>{{ $description }}</p>
            @endif
        </div>
    </div>
    @isset($actions)
        <div class="workspace-page-header-actions">
            {{ $actions }}
        </div>
    @endisset
</header>
