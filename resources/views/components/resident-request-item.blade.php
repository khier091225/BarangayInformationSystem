@props(['serviceRequest', 'showStatus' => true])

<a href="{{ route('account.requests.show', $serviceRequest) }}" {{ $attributes->class(['resident-request-row']) }}>
    <span class="resident-section-icon {{ $serviceRequest->type === 'blotter' ? 'resident-section-icon-warm' : '' }}"><i data-lucide="{{ $serviceRequest->type === 'certificate' ? 'files' : 'notebook-pen' }}" aria-hidden="true"></i></span>
    <span class="resident-request-copy">
        <span class="resident-request-reference">#{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }} <span aria-hidden="true">·</span> {{ $serviceRequest->type === 'certificate' ? 'Document request' : 'Incident report' }}</span>
        <strong>{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</strong>
        <span class="resident-request-date">{{ $serviceRequest->reviewed_at ? 'Reviewed '.$serviceRequest->reviewed_at->format('M j, Y') : 'Submitted '.$serviceRequest->created_at->format('M j, Y') }}@if ($serviceRequest->response_note)<span class="resident-response-indicator"><i data-lucide="messages-square" aria-hidden="true"></i> Staff response</span>@endif</span>
    </span>
    <span class="resident-request-trailing">
        @if ($showStatus)
            <x-request-status :status="$serviceRequest->status" />
        @endif
        <span class="resident-request-open">View details <i data-lucide="arrow-right" aria-hidden="true"></i></span>
    </span>
</a>
