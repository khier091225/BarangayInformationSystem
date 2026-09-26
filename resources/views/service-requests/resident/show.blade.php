@extends('layouts.resident')

@section('title', 'Request Details | Barangay Information System')

@section('content')
    <a href="{{ route('account.requests.index') }}" class="resident-page-back"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to my requests</a>
    <div class="resident-page-heading resident-request-heading">
        <div>
            <div class="resident-request-meta">
                <span class="resident-kicker resident-kicker-dark">REQUEST #{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
                <x-request-status :status="$serviceRequest->status" />
            </div>
            <h1>{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</h1>
            <p>Review your submission and any updates from barangay staff.</p>
        </div>
    </div>

    <ol class="resident-progress" aria-label="Request progress">
        <li class="resident-progress-done"><span class="resident-progress-number"><i data-lucide="check" aria-hidden="true"></i></span><div><strong>Submitted</strong><span>{{ $serviceRequest->created_at->format('M j, Y') }}</span></div></li>
        <li class="{{ $serviceRequest->status === 'Pending' ? 'resident-progress-current' : 'resident-progress-done' }}" @if ($serviceRequest->status === 'Pending') aria-current="step" @endif><span class="resident-progress-number">2</span><div><strong>Staff review</strong><span>{{ $serviceRequest->status === 'Pending' ? 'Waiting for staff action' : ($serviceRequest->reviewed_at?->format('M j, Y') ?? 'Review finished') }}</span></div></li>
        <li class="{{ $serviceRequest->status === 'Pending' ? '' : ($serviceRequest->status === 'Declined' ? 'resident-progress-declined' : 'resident-progress-done') }}" @if ($serviceRequest->status !== 'Pending') aria-current="step" @endif><span class="resident-progress-number">3</span><div><strong>{{ $serviceRequest->status === 'Pending' ? 'Result' : $serviceRequest->status }}</strong><span>{{ $serviceRequest->status === 'Pending' ? 'Your outcome will appear here' : 'See details below' }}</span></div></li>
    </ol>

    <section class="resident-card resident-detail-card" aria-labelledby="request-details-title">
        <div class="resident-detail-heading">
            <div><h2 id="request-details-title">Request details</h2><p>Submitted {{ $serviceRequest->created_at->format('M d, Y \a\t h:i A') }}</p></div>
            <span class="resident-section-icon"><i data-lucide="{{ $serviceRequest->type === 'certificate' ? 'files' : 'notebook-pen' }}" aria-hidden="true"></i></span>
        </div>
        <dl class="resident-detail-grid">
            @if ($serviceRequest->type === 'certificate')
                <div><dt>Document</dt><dd>{{ $serviceRequest->certificate_type }}</dd></div>
                <div><dt>Purpose</dt><dd>{{ $serviceRequest->purpose }}</dd></div>
            @else
                <div><dt>Respondent</dt><dd>{{ $serviceRequest->respondent }}</dd></div>
                <div><dt>Incident date</dt><dd>{{ $serviceRequest->incident_date->format('M d, Y') }}</dd></div>
                <div><dt>Incident details</dt><dd>{{ $serviceRequest->incident }}</dd></div>
            @endif
        </dl>

        @if ($serviceRequest->response_note)
            <div class="resident-detail-note"><strong><i data-lucide="messages-square" aria-hidden="true"></i> Message from barangay staff</strong>@if ($serviceRequest->reviewed_at)<time datetime="{{ $serviceRequest->reviewed_at->toIso8601String() }}">{{ $serviceRequest->reviewed_at->format('M j, Y · g:i A') }}</time>@endif<p>{{ $serviceRequest->response_note }}</p></div>
        @endif

        @if ($serviceRequest->status === 'Completed')
            <p class="resident-detail-guidance"><i data-lucide="badge-check" aria-hidden="true"></i><span>{{ $serviceRequest->type === 'certificate' ? 'Your document has been issued. Please contact barangay staff about collection.' : 'Your report has been added to the barangay blotter records.' }}</span></p>
        @elseif ($serviceRequest->status === 'Pending')
            <p class="resident-detail-guidance"><i data-lucide="calendar-clock" aria-hidden="true"></i><span>Barangay staff will review this request. Check this page for updates.</span></p>
        @elseif ($serviceRequest->status === 'Declined')
            <p class="resident-detail-guidance"><i data-lucide="info" aria-hidden="true"></i><span>Your request was declined. Review the staff message above, if provided, or contact barangay staff for clarification.</span></p>
        @endif
    </section>
@endsection
