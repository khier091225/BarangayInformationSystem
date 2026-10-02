@props(['status'])

@php
    $label = $status === \App\Models\Blotter::STATUS_MEDIATION ? 'Under Mediation' : $status;
@endphp

<span {{ $attributes->class(['incident-status', 'incident-status-'.strtolower($status)]) }}>{{ $label }}</span>
