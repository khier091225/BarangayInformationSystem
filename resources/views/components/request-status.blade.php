@props(['status'])

<span {{ $attributes->class(['resident-badge', 'resident-badge-awaiting-payment' => $status === 'Awaiting Payment', 'resident-badge-completed' => $status === 'Completed', 'resident-badge-declined' => $status === 'Declined']) }}>{{ $status }}</span>
