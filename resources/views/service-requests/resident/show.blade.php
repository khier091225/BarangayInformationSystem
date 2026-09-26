@extends('layouts.resident')

@section('title', 'Request Details | Barangay Information System')

@section('content')
    <a href="{{ route('account.requests.index') }}" class="resident-page-back"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to my requests</a>
    <div class="resident-page-heading resident-request-heading">
        <div>
            <div class="resident-request-meta">
                <span class="resident-kicker resident-kicker-dark">REQUEST #{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="resident-badge @if ($serviceRequest->status === 'Completed') resident-badge-completed @elseif ($serviceRequest->status === 'Declined') resident-badge-declined @endif">{{ $serviceRequest->status }}</span>
            </div>
            <h1>{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</h1>
            <p>Review your submission and any updates from barangay staff.</p>
        </div>
    </div>

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
            <div class="resident-detail-note"><strong>Message from barangay staff</strong><p>{{ $serviceRequest->response_note }}</p></div>
        @endif

        @if ($serviceRequest->status === 'Completed')
            <p class="resident-detail-guidance"><i data-lucide="badge-check" aria-hidden="true"></i><span>{{ $serviceRequest->type === 'certificate' ? 'Your document has been issued. Please contact barangay staff about collection.' : 'Your report has been added to the barangay blotter records.' }}</span></p>
        @elseif ($serviceRequest->status === 'Pending')
            <p class="resident-detail-guidance"><i data-lucide="calendar-clock" aria-hidden="true"></i><span>Barangay staff will review this request. Check this page for updates.</span></p>
        @endif
    </section>
@endsection
