@extends('layouts.resident')

@section('title', 'Incident Report '.$incidentReport->reference_number.' | Barangay Information System')

@section('content')
    <div class="incident-detail-page">
        <div class="incident-detail-back"><a href="{{ route('account.incidents.index') }}" class="resident-inline-link"><i data-lucide="arrow-left" aria-hidden="true"></i> My incident reports</a></div>
        <header class="resident-request-heading">
            <span class="resident-request-type-icon"><i data-lucide="message-square-warning" aria-hidden="true"></i></span>
            <div><div class="resident-request-meta"><span class="resident-kicker resident-kicker-dark">{{ $incidentReport->reference_number }}</span><x-incident-status :status="$incidentReport->status" /></div><h1>{{ $incidentReport->categoryLabel() }}</h1><p>Submitted {{ $incidentReport->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</p></div>
        </header>

        <section class="incident-status-banner incident-status-banner-{{ strtolower($incidentReport->status) }}" aria-labelledby="incident-status-title">
            <i data-lucide="{{ $incidentReport->status === 'Closed' || $incidentReport->status === 'Resolved' ? 'badge-check' : 'messages-square' }}" aria-hidden="true"></i>
            <div><h2 id="incident-status-title">{{ match ($incidentReport->status) { 'Submitted' => 'Your report has been received', 'Assigned' => 'Your report has been assigned', 'Responding' => 'Barangay staff are responding', 'Resolved' => 'Staff marked this report resolved', default => 'This report is closed' } }}</h2>
                <p>{{ $incidentReport->status === 'Submitted' ? 'Barangay staff will review it and assign a handler. This page will show their updates.' : ($incidentReport->assignedTeamLabel() ? 'Handling team: '.$incidentReport->assignedTeamLabel().'.' : 'Check the updates below for more information.') }}</p></div>
        </section>

        <div class="incident-detail-grid">
            <div class="incident-detail-main">
                <section class="resident-card incident-detail-card" aria-labelledby="incident-details-title">
                    <div class="resident-detail-heading"><span class="resident-section-icon"><i data-lucide="clipboard-list" aria-hidden="true"></i></span><h2 id="incident-details-title">Report details</h2></div>
                    <dl class="incident-detail-fields">
                        <div><dt>Category</dt><dd>{{ $incidentReport->categoryLabel() }}</dd></div>
                        <div><dt>Date and time</dt><dd>{{ $incidentReport->occurred_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</dd></div>
                        <div><dt>Location</dt><dd>{{ $incidentReport->location }}</dd></div>
                        <div><dt>Identity preference</dt><dd>{{ $incidentReport->keep_identity_confidential ? 'Confidential to unassigned staff' : 'Standard report' }}</dd></div>
                        <div class="incident-detail-wide"><dt>Description</dt><dd>{{ $incidentReport->description }}</dd></div>
                        @if ($incidentReport->evidence_path)
                            <div class="incident-detail-wide"><dt>Attachment</dt><dd><a href="{{ route('incident-reports.evidence', $incidentReport) }}" class="resident-inline-link"><i data-lucide="download" aria-hidden="true"></i> Download {{ $incidentReport->evidence_original_name }}</a></dd></div>
                        @endif
                    </dl>
                </section>
            </div>
            <aside class="resident-card incident-timeline-card" aria-labelledby="incident-timeline-title">
                <div class="resident-detail-heading"><span class="resident-section-icon"><i data-lucide="list-checks" aria-hidden="true"></i></span><h2 id="incident-timeline-title">Report updates</h2></div>
                <ol class="incident-timeline">
                    @foreach ($incidentReport->updates as $update)
                        <li><span class="incident-timeline-dot" aria-hidden="true"></span><div><x-incident-status :status="$update->status" /><time datetime="{{ $update->created_at->toIso8601String() }}">{{ $update->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time><p>{{ $update->message }}</p></div></li>
                    @endforeach
                </ol>
                <p class="incident-timeline-footnote">For immediate danger, contact emergency services or the barangay office directly. Online reports may not be reviewed immediately.</p>
            </aside>
        </div>
    </div>
@endsection
