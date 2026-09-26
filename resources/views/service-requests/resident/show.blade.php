<div>
    <!-- When there is no desire, all things are at peace. - Laozi -->
</div>
@extends('layouts.resident')

@section('title', 'Request Details | Barangay Information System')

@section('content')
    <a href="{{ route('account.requests.index') }}" class="resident-muted">← Back to my requests</a>
    <div style="margin: 18px 0 24px;">
        <div class="eyebrow">REQUEST #{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</div>
        <h1 class="resident-heading">{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</h1>
        <span class="resident-badge @if ($serviceRequest->status === 'Completed') resident-badge-completed @elseif ($serviceRequest->status === 'Declined') resident-badge-declined @endif">{{ $serviceRequest->status }}</span>
    </div>

    <div class="resident-card" style="max-width: 720px;">
        <p class="resident-muted"><strong>Submitted:</strong> {{ $serviceRequest->created_at->format('M d, Y h:i A') }}</p>
        @if ($serviceRequest->type === 'certificate')
            <p><strong>Document:</strong> {{ $serviceRequest->certificate_type }}</p>
            <p><strong>Purpose:</strong> {{ $serviceRequest->purpose }}</p>
        @else
            <p><strong>Respondent:</strong> {{ $serviceRequest->respondent }}</p>
            <p><strong>Incident date:</strong> {{ $serviceRequest->incident_date->format('M d, Y') }}</p>
            <p style="white-space: pre-wrap;"><strong>Incident details:</strong> {{ $serviceRequest->incident }}</p>
        @endif

        @if ($serviceRequest->response_note)
            <div style="background: #f4f8f4; border-radius: 8px; padding: 16px; margin-top: 20px;">
                <strong>Message from barangay staff</strong>
                <p style="white-space: pre-wrap; margin-bottom: 0;">{{ $serviceRequest->response_note }}</p>
            </div>
        @endif

        @if ($serviceRequest->status === 'Completed')
            <p class="resident-muted" style="margin-top: 20px;">{{ $serviceRequest->type === 'certificate' ? 'Your document has been issued. Please contact barangay staff about collection.' : 'Your report has been added to the barangay blotter records.' }}</p>
        @elseif ($serviceRequest->status === 'Pending')
            <p class="resident-muted" style="margin-top: 20px;">Barangay staff will review this request. Check this page for updates.</p>
        @endif
    </div>
@endsection
