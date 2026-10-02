@extends('layouts.app')

@section('title', 'Incident Report '.$incidentReport->reference_number.' | Barangay Information System')

@section('breadcrumb')
    <a href="{{ route('incident-reports.index') }}">Incident reports</a><i data-lucide="chevron-right" aria-hidden="true"></i><strong>{{ $incidentReport->reference_number }}</strong>
@endsection

@section('content')
    <div class="staff-requests-page">
        <header class="staff-request-detail-heading"><span class="staff-request-detail-icon"><i data-lucide="message-square-warning" aria-hidden="true"></i></span><div><div class="staff-request-detail-meta"><span>{{ $incidentReport->reference_number }}</span><x-incident-status :status="$incidentReport->status" /></div><h1>{{ $incidentReport->categoryLabel() }}</h1><p>Submitted {{ $incidentReport->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</p></div></header>
        <div class="staff-request-detail-grid incident-staff-grid">
            <div class="incident-detail-main">
                <section class="staff-request-detail-card" aria-labelledby="staff-incident-details-title">
                    <div class="staff-request-card-heading"><span class="staff-request-card-icon"><i data-lucide="clipboard-list" aria-hidden="true"></i></span><div><h2 id="staff-incident-details-title">Incident details</h2><p>Review the report before taking responsibility.</p></div></div>
                    <dl class="staff-request-fields">
                        <div><dt>Category</dt><dd>{{ $incidentReport->categoryLabel() }}</dd></div>
                        <div><dt>Occurred</dt><dd>{{ $incidentReport->occurred_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</dd></div>
                        <div><dt>Location</dt><dd>{{ $incidentReport->location }}</dd></div>
                        <div><dt>Suggested team</dt><dd>{{ $incidentReport->suggestedTeamLabel() }}</dd></div>
                        <div><dt>Resident</dt><dd>{{ $canViewIdentity ? ($incidentReport->resident?->full_name ?? 'Resident record unavailable') : ($incidentReport->status === 'Submitted' ? 'Confidential until you accept this report' : 'Confidential to the assigned staff member') }}</dd></div>
                        <div><dt>Assigned team</dt><dd>{{ $incidentReport->assignedTeamLabel() ?? 'Awaiting assignment' }}</dd></div>
                        <div class="staff-request-field-long"><dt>What happened</dt><dd>{{ $incidentReport->description }}</dd></div>
                        @if ($incidentReport->evidence_path && $canViewIdentity)
                            <div class="staff-request-field-long"><dt>Attachment</dt><dd><a href="{{ route('incident-reports.evidence', $incidentReport) }}"><i data-lucide="download" aria-hidden="true"></i> Download {{ $incidentReport->evidence_original_name }}</a></dd></div>
                        @endif
                    </dl>
                    @if ($incidentReport->keep_identity_confidential)
                        <p class="incident-privacy-note"><i data-lucide="lock-keyhole" aria-hidden="true"></i> Confidential report. The resident's identity is hidden from staff who are not handling it. Avoid including identifying details in messages or alerts.</p>
                    @endif
                </section>
                <section class="staff-request-detail-card" aria-labelledby="staff-incident-alert-title">
                    <div class="staff-request-card-heading"><span class="staff-request-card-icon"><i data-lucide="bell-ring" aria-hidden="true"></i></span><div><h2 id="staff-incident-alert-title">Duty alerts</h2><p>Alerts are attempted when the resident submits the report; accepting it is not required.</p></div></div>
                    <dl class="staff-request-fields">
                        <div><dt>Assigned alert team</dt><dd>{{ $incidentReport->suggestedTeamLabel() }}</dd></div>
                        <div><dt>SMS</dt><dd>{{ match ($incidentReport->alert_sms_status) { 'submitted' => 'Accepted by SMS service; phone delivery unverified', 'unconfirmed' => 'Submission unconfirmed; check PhilSMS before retrying', 'failed' => 'Submission failed', 'not_configured' => 'No phone configured', default => filled($alertRecipients['phone']) ? 'Awaiting submission' : 'No phone configured' } }}</dd></div>
                        <div><dt>Email</dt><dd>{{ match ($incidentReport->alert_email_status) { 'submitted' => 'Sent to mail service; inbox delivery unverified', 'failed' => 'Submission failed', 'not_configured' => 'No email configured', default => filled($alertRecipients['email']) ? 'Awaiting submission' : 'No email configured' } }}</dd></div>
                    </dl>
                </section>
                <section class="staff-request-detail-card incident-staff-timeline" aria-labelledby="staff-incident-timeline-title">
                    <div class="staff-request-card-heading"><span class="staff-request-card-icon"><i data-lucide="list-checks" aria-hidden="true"></i></span><div><h2 id="staff-incident-timeline-title">Status history</h2><p>These messages are visible to the resident.</p></div></div>
                    <ol class="incident-timeline">
                        @foreach ($incidentReport->updates as $update)
                            <li><span class="incident-timeline-dot" aria-hidden="true"></span><div><x-incident-status :status="$update->status" /><time datetime="{{ $update->created_at->toIso8601String() }}">{{ $update->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time><p>{{ $update->message }}</p></div></li>
                        @endforeach
                    </ol>
                </section>
            </div>
            <aside class="staff-request-detail-card incident-action-card" aria-labelledby="staff-incident-action-title">
                <div class="staff-request-card-heading"><span class="staff-request-card-icon"><i data-lucide="user-round-check" aria-hidden="true"></i></span><div><h2 id="staff-incident-action-title">Handle this report</h2><p>Every status change is shared with the resident.</p></div></div>
                @if ($incidentReport->status === 'Submitted')
                    <form method="POST" action="{{ route('incident-reports.update', $incidentReport) }}" class="incident-action-form">
                        @csrf
                        <input type="hidden" name="action" value="accept">
                        <p class="incident-action-help">Suggested team: <strong>{{ $incidentReport->suggestedTeamLabel() }}</strong>. Accepting assigns this report to you and sends the resident an automatic update.</p>
                        <details class="complaint-optional-fields" @if ($errors->has('team') || $errors->has('message')) open @endif>
                            <summary>Change team or add a message <span>(optional)</span></summary>
                            <div class="complaint-optional-body">
                                <div class="resident-field"><x-form.label for="team" required>Assign team</x-form.label><x-form.select name="team" required :aria-invalid="$errors->has('team') ? 'true' : 'false'">@foreach (\App\Models\IncidentReport::TEAM_LABELS as $value => $label)<option value="{{ $value }}" @selected(old('team', $incidentReport->suggested_team) === $value)>{{ $label }}</option>@endforeach</x-form.select><x-form.error :message="$errors->first('team')" /></div>
                                <div class="resident-field"><x-form.label for="message">Message to resident</x-form.label><x-form.textarea name="message" rows="4" maxlength="2000" placeholder="Add a personal update if needed.">{{ old('message') }}</x-form.textarea><x-form.error :message="$errors->first('message')" /></div>
                            </div>
                        </details>
                        <button type="submit" class="button button-primary">Accept report <i data-lucide="arrow-right" aria-hidden="true"></i></button>
                    </form>
                @elseif ($incidentReport->assigned_to === auth()->id() && $incidentReport->nextStatus())
                    <form method="POST" action="{{ route('incident-reports.update', $incidentReport) }}" class="incident-action-form">
                        @csrf
                        <input type="hidden" name="action" value="advance">
                        <p class="incident-action-help">Next status: <strong>{{ $incidentReport->nextStatus() }}</strong></p>
                        <p class="incident-action-help">The resident will see the new status. Add a personal note only if there is more to share.</p>
                        <details class="complaint-optional-fields" @if ($errors->has('message')) open @endif>
                            <summary>Add a message to the resident <span>(optional)</span></summary>
                            <div class="complaint-optional-body"><div class="resident-field"><x-form.label for="message">Message to resident</x-form.label><x-form.textarea name="message" rows="4" maxlength="2000" placeholder="Share a helpful update if needed." :aria-invalid="$errors->has('message') ? 'true' : 'false'">{{ old('message') }}</x-form.textarea><x-form.error :message="$errors->first('message')" /></div></div>
                        </details>
                        <button type="submit" class="button button-primary">Mark {{ $incidentReport->nextStatus() }} <i data-lucide="arrow-right" aria-hidden="true"></i></button>
                    </form>
                @else
                    <p class="incident-action-help">{{ $incidentReport->status === 'Closed' ? 'This report is closed.' : 'Assigned to '.$incidentReport->assignee?->name.'. Only the assigned staff member can update its status.' }}</p>
                @endif
            </aside>
        </div>
    </div>
@endsection
