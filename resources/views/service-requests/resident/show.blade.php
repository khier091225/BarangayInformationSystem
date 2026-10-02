@extends('layouts.resident')

@section('title', 'Request Details | Barangay Information System')

@section('content')
    <div class="resident-detail-page">
        <div class="resident-detail-back"><a href="{{ route('account.requests.index') }}" class="resident-inline-link"><i data-lucide="arrow-left" aria-hidden="true"></i> My requests</a></div>
        <header class="resident-request-heading">
            <span class="resident-request-type-icon"><i data-lucide="{{ $serviceRequest->type === 'certificate' ? 'files' : 'notebook-pen' }}" aria-hidden="true"></i></span>
            <div>
                <div class="resident-request-meta">
                    <span class="resident-kicker resident-kicker-dark">REQUEST #{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <x-request-status :status="$serviceRequest->status" />
                </div>
                <h1>{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</h1>
                <p>Submitted {{ $serviceRequest->created_at->timezone('Asia/Manila')->format('M j, Y') }}</p>
            </div>
        </header>

        <section class="resident-request-status resident-request-status-{{ strtolower($serviceRequest->status) }}" aria-labelledby="request-status-title">
            <span class="resident-request-status-icon"><i data-lucide="{{ $serviceRequest->status === 'Pending' ? 'calendar-clock' : ($serviceRequest->status === 'Completed' ? 'badge-check' : 'info') }}" aria-hidden="true"></i></span>
            <div>
                @if ($serviceRequest->status === 'Completed')
                    <h2 id="request-status-title">{{ $serviceRequest->type === 'certificate' ? 'Your document has been issued' : 'Your report has been recorded' }}</h2>
                    <p>{{ $serviceRequest->type === 'certificate' ? 'Contact barangay staff about collecting your document.' : 'Your report has been added to the official barangay blotter records. Contact staff for any follow-up on the incident.' }} @if ($serviceRequest->response_note) Check their message below for any instructions. @endif</p>
                @elseif ($serviceRequest->status === 'Declined')
                    <h2 id="request-status-title">This request was declined</h2>
                    <p>{{ $serviceRequest->response_note ? 'Read the staff message below for feedback. Contact the barangay office if you need clarification.' : 'Contact barangay staff for the reason and guidance on what to do next.' }}</p>
                @else
                    <h2 id="request-status-title">Waiting for staff review</h2>
                    <p>Your request has been received. Barangay staff will review the details, and the result will appear on this page.</p>
                @endif
            </div>
            @if ($serviceRequest->response_note)
                <a href="#staff-message" class="resident-inline-link">Read staff message <i data-lucide="arrow-right" aria-hidden="true"></i></a>
            @endif
        </section>

        <div class="resident-request-layout">
            <div class="resident-request-content">
                <section class="resident-card resident-detail-card" aria-labelledby="request-details-title">
                    <div class="resident-detail-heading">
                        <span class="resident-section-icon"><i data-lucide="clipboard-list" aria-hidden="true"></i></span>
                        <div><h2 id="request-details-title">Request details</h2></div>
                    </div>
                    <dl class="resident-detail-grid">
                        @if ($serviceRequest->type === 'certificate')
                            <div class="resident-detail-wide"><dt>Document requested</dt><dd>{{ $serviceRequest->certificate_type }}</dd></div>
                            <div class="resident-detail-description"><dt>Purpose of request</dt><dd>{{ $serviceRequest->purpose }}</dd></div>
                        @else
                            <div><dt>Respondent</dt><dd>{{ $serviceRequest->respondent }}</dd></div>
                            <div><dt>Incident date</dt><dd>{{ $serviceRequest->incident_date->format('M j, Y') }}</dd></div>
                            <div class="resident-detail-description"><dt>Incident details</dt><dd>{{ $serviceRequest->incident }}</dd></div>
                        @endif
                    </dl>
                </section>

                @if ($serviceRequest->type === \App\Models\ServiceRequest::TYPE_BLOTTER && $serviceRequest->blotter)
                    <section class="resident-card incident-timeline-card" aria-labelledby="blotter-progress-title">
                        <div class="resident-detail-heading">
                            <span class="resident-section-icon"><i data-lucide="history" aria-hidden="true"></i></span>
                            <div>
                                <h2 id="blotter-progress-title">Blotter case progress</h2>
                                <p>Current status: <x-blotter-status :status="$serviceRequest->blotter->status" /></p>
                            </div>
                        </div>
                        @if ($serviceRequest->blotter->hearing_at)
                            <p class="incident-timeline-footnote">Mediation schedule: <strong>{{ $serviceRequest->blotter->hearing_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</strong></p>
                        @endif
                        <ol class="incident-timeline">
                            <li>
                                <span class="incident-timeline-dot" aria-hidden="true"></span>
                                <div><x-blotter-status :status="\App\Models\Blotter::STATUS_PENDING" /><time datetime="{{ $serviceRequest->blotter->created_at->toIso8601String() }}">{{ $serviceRequest->blotter->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time><p>Your report was added to the official barangay blotter.</p></div>
                            </li>
                            @foreach ($serviceRequest->blotter->updates as $update)
                                <li>
                                    <span class="incident-timeline-dot" aria-hidden="true"></span>
                                    <div><x-blotter-status :status="$update->status" /><time datetime="{{ $update->created_at->toIso8601String() }}">{{ $update->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time><p>{{ $update->message }}</p></div>
                                </li>
                            @endforeach
                        </ol>
                    </section>
                @endif

                @if ($serviceRequest->status !== 'Pending' || $serviceRequest->response_note)
                <section id="staff-message" class="resident-card resident-staff-message" aria-labelledby="staff-message-title" tabindex="-1">
                    <div class="resident-detail-heading">
                        <span class="resident-section-icon"><i data-lucide="messages-square" aria-hidden="true"></i></span>
                        <div>
                            <h2 id="staff-message-title">Message from barangay staff</h2>
                            @if ($serviceRequest->response_note && $serviceRequest->reviewed_at)
                                <p><time datetime="{{ $serviceRequest->reviewed_at->toIso8601String() }}">{{ $serviceRequest->reviewed_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time></p>
                            @endif
                        </div>
                    </div>
                    @if ($serviceRequest->response_note)
                        <p class="resident-staff-message-text">{{ $serviceRequest->response_note }}</p>
                    @else
                        <div class="resident-staff-message-empty">
                            <strong>{{ $serviceRequest->status === 'Pending' ? 'No message yet' : 'No additional message' }}</strong>
                            <p>{{ $serviceRequest->status === 'Pending' ? 'Any feedback from staff will appear here after your request is reviewed.' : 'Staff did not leave a message with this decision. You can contact the barangay office if you have questions.' }}</p>
                        </div>
                    @endif
                </section>
                @endif
            </div>

            <aside class="resident-request-sidebar" aria-label="Request progress and follow-up">
                <section class="resident-card resident-progress-card" aria-labelledby="request-progress-title">
                    <div class="resident-progress-heading"><h2 id="request-progress-title">Request progress</h2></div>
                    <ol class="resident-progress" aria-label="Request progress">
                        <li class="resident-progress-done">
                            <span class="resident-progress-number"><i data-lucide="check" aria-hidden="true"></i></span>
                            <div><strong>Request submitted</strong><time datetime="{{ $serviceRequest->created_at->toIso8601String() }}">{{ $serviceRequest->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time></div>
                        </li>
                        <li class="{{ $serviceRequest->status === 'Pending' ? 'resident-progress-current' : 'resident-progress-done' }}" @if ($serviceRequest->status === 'Pending') aria-current="step" @endif>
                            <span class="resident-progress-number">
                                @if ($serviceRequest->status === 'Pending')
                                    <i data-lucide="calendar-clock" aria-hidden="true"></i>
                                @else
                                    <i data-lucide="check" aria-hidden="true"></i>
                                @endif
                            </span>
                            <div>
                                <strong>Staff review</strong>
                                @if ($serviceRequest->status === 'Pending')
                                    <span class="resident-progress-label">Current step</span>
                                @elseif ($serviceRequest->reviewed_at)
                                    <time datetime="{{ $serviceRequest->reviewed_at->toIso8601String() }}">{{ $serviceRequest->reviewed_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time>
                                @endif
                            </div>
                        </li>
                        <li class="{{ $serviceRequest->status === 'Pending' ? 'resident-progress-upcoming' : ($serviceRequest->status === 'Declined' ? 'resident-progress-declined' : 'resident-progress-done') }}" @if ($serviceRequest->status !== 'Pending') aria-current="step" @endif>
                            <span class="resident-progress-number">
                                @if ($serviceRequest->status === 'Pending')
                                    3
                                @else
                                    <i data-lucide="{{ $serviceRequest->status === 'Completed' ? 'check' : 'x' }}" aria-hidden="true"></i>
                                @endif
                            </span>
                            <div>
                                <strong>{{ $serviceRequest->status === 'Pending' ? 'Request result' : ($serviceRequest->status === 'Completed' ? ($serviceRequest->type === 'certificate' ? 'Document issued' : 'Report recorded') : 'Request declined') }}</strong>
                            </div>
                        </li>
                    </ol>
                </section>

                <section class="resident-follow-up" aria-labelledby="follow-up-title">
                    <i data-lucide="info" aria-hidden="true"></i>
                    <div><h2 id="follow-up-title">Need to follow up?</h2><p>When contacting barangay staff, mention <strong>request #{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</strong> so they can find your submission.</p></div>
                </section>
            </aside>
        </div>
    </div>
@endsection
