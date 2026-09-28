@props(['status'])

<span {{ $attributes->class(['incident-status', 'incident-status-'.strtolower($status)]) }}>{{ $status }}</span>
