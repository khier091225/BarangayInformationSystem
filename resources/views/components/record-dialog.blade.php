@props(['id', 'title', 'description' => null, 'icon' => 'plus', 'openOnLoad' => false, 'wide' => false, 'compact' => false])

<x-dialog :id="$id" :title="$title" :description="$description" :icon="$icon" :open-on-load="$openOnLoad" :wide="$wide" :compact="$compact" class="record-dialog" data-record-dialog {{ $attributes }}>
    {{ $slot }}
</x-dialog>
