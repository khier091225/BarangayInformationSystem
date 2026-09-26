@props(['status'])

<span {{ $attributes->class(['resident-badge', 'resident-badge-completed' => $status === 'Completed', 'resident-badge-declined' => $status === 'Declined']) }}>{{ $status }}</span>
