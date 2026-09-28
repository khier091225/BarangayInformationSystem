@extends('layouts.resident')

@section('title', 'My Dashboard | Barangay Information System')

@section('content')
    <header class="resident-welcome">
        <div><span class="resident-kicker resident-kicker-dark">YOUR BARANGAY, CONNECTED</span><h1>Hello, {{ $user->name }}</h1><p>Request a service, follow its progress, and hear back from barangay staff.</p></div>
        <span class="resident-account-state"><i data-lucide="{{ $user->role === 'staff' ? 'user-round' : ($user->resident_id === null ? 'info' : 'badge-check') }}" aria-hidden="true"></i>{{ $user->role === 'staff' ? 'Staff account' : ($user->resident_id === null ? 'Verification needed' : 'Verified resident') }}</span>
    </header>

    @if ($user->role === 'staff')
        <section class="resident-card resident-single-panel" aria-labelledby="staff-panel-title">
            <span class="resident-section-icon"><i data-lucide="layout-dashboard" aria-hidden="true"></i></span>
            <h2 id="staff-panel-title">Staff workspace</h2>
            <p>Manage barangay records and review resident requests from the staff dashboard.</p>
            <a href="{{ route('dashboard') }}" class="resident-button resident-button-primary">Open staff workspace <i data-lucide="arrow-right" aria-hidden="true"></i></a>
        </section>
    @elseif ($user->resident_id === null)
        <div class="resident-content-grid resident-verification-grid">
            <section class="resident-card resident-form-card" aria-labelledby="verification-title">
                <span class="resident-section-icon"><i data-lucide="badge-check" aria-hidden="true"></i></span>
                <h2 id="verification-title">Verify your resident record</h2>
                <p class="resident-muted">Your account is not linked to a verified resident record. Ask barangay staff for a registration code before submitting requests.</p>
                <form method="POST" action="{{ route('account.verify') }}" class="resident-form">
                    @csrf
                    <div class="resident-field">
                        <x-form.label for="registration_code" required>Registration code</x-form.label>
                        <x-form.input name="registration_code" :value="old('registration_code')" required autocomplete="off" placeholder="XXXX-XXXX-XXXX-XXXX" :aria-invalid="$errors->has('registration_code') ? 'true' : 'false'" :aria-describedby="$errors->has('registration_code') ? 'registration-code-error' : null" />
                        <x-form.error id="registration-code-error" :message="$errors->first('registration_code')" />
                    </div>
                    <button type="submit" class="resident-button resident-button-primary">Verify resident record <i data-lucide="arrow-right" aria-hidden="true"></i></button>
                </form>
            </section>
            <aside class="resident-card resident-help-card" aria-labelledby="verification-help-title">
                <span class="resident-section-icon resident-section-icon-warm"><i data-lucide="info" aria-hidden="true"></i></span>
                <h2 id="verification-help-title">How verification works</h2>
                <ol class="resident-steps"><li>Ask barangay staff to check your resident record and mobile number.</li><li>Check your registered mobile number for the SMS registration code.</li><li>Enter the code here to unlock online requests.</li></ol>
            </aside>
        </div>
    @else
        <section class="resident-services" id="resident-services" aria-labelledby="services-title">
            <div class="resident-section-heading"><h2 id="services-title">What do you need today?</h2></div>
            <div class="resident-service-grid">
                <a href="{{ route('account.requests.certificate.create') }}" class="resident-service-card resident-service-documents">
                    <span class="resident-service-top"><span class="resident-service-icon"><i data-lucide="files" aria-hidden="true"></i></span><span>DOCUMENT SERVICES</span><i data-lucide="arrow-up-right" aria-hidden="true"></i></span>
                    <strong>Request a document</strong>
                    <span class="resident-service-description">Barangay Clearance, Certificate of Residency, Certificate of Indigency, or Business Clearance.</span>
                    <span class="resident-service-action">Start request <i data-lucide="arrow-right" aria-hidden="true"></i></span>
                </a>
                <a href="{{ route('account.requests.blotter.create') }}" class="resident-service-card resident-service-report">
                    <span class="resident-service-top"><span class="resident-service-icon"><i data-lucide="notebook-pen" aria-hidden="true"></i></span><span>INCIDENT REPORT</span><i data-lucide="arrow-up-right" aria-hidden="true"></i></span>
                    <strong>File a blotter report</strong>
                    <span class="resident-service-description">Share incident details for barangay staff to review and record.</span>
                    <span class="resident-service-action">File report <i data-lucide="arrow-right" aria-hidden="true"></i></span>
                </a>
                <a href="{{ route('account.incidents.create') }}" class="resident-service-card resident-service-incident">
                    <span class="resident-service-top"><span class="resident-service-icon"><i data-lucide="message-square-warning" aria-hidden="true"></i></span><span>ONLINE SUMBONG</span><i data-lucide="arrow-up-right" aria-hidden="true"></i></span>
                    <strong>Report an incident</strong>
                    <span class="resident-service-description">Report community concerns such as noise, sanitation, parking, or infrastructure and track staff updates.</span>
                    <span class="resident-service-action">Start report <i data-lucide="arrow-right" aria-hidden="true"></i></span>
                </a>
            </div>
        </section>

        @if ($latestIncidentReport)
            <section class="resident-card incident-dashboard-update" aria-labelledby="latest-incident-title">
                <span class="resident-section-icon"><i data-lucide="message-square-warning" aria-hidden="true"></i></span>
                <div><span class="resident-kicker resident-kicker-dark">LATEST INCIDENT REPORT</span><h2 id="latest-incident-title">{{ $latestIncidentReport->categoryLabel() }}</h2><p>{{ $latestIncidentReport->reference_number }} · {{ $latestIncidentReport->location }}</p></div>
                <x-incident-status :status="$latestIncidentReport->status" />
                <a href="{{ route('account.incidents.show', $latestIncidentReport) }}" class="resident-inline-link">View progress <i data-lucide="arrow-right" aria-hidden="true"></i></a>
            </section>
        @endif

        <section class="resident-overview" aria-labelledby="overview-title">
            <div class="resident-section-heading"><h2 id="overview-title">Your requests at a glance</h2></div>
            <div class="resident-stats">
                <a href="{{ route('account.requests.index') }}" class="resident-stat">
                    <span class="resident-stat-icon"><i data-lucide="files" aria-hidden="true"></i></span><span class="resident-stat-label">Total requests</span><strong>{{ number_format($requestCounts['total']) }}</strong>
                </a>
                <a href="{{ route('account.requests.index', ['status' => 'Pending']) }}" class="resident-stat resident-stat-pending">
                    <span class="resident-stat-icon"><i data-lucide="calendar-clock" aria-hidden="true"></i></span><span class="resident-stat-label">Pending</span><strong>{{ number_format($requestCounts['pending']) }}</strong>
                </a>
                <a href="{{ route('account.requests.index', ['status' => 'Completed']) }}" class="resident-stat resident-stat-completed">
                    <span class="resident-stat-icon"><i data-lucide="file-check-2" aria-hidden="true"></i></span><span class="resident-stat-label">Completed</span><strong>{{ number_format($requestCounts['completed']) }}</strong>
                </a>
                <a href="{{ route('account.requests.index', ['status' => 'Declined']) }}" class="resident-stat resident-stat-declined">
                    <span class="resident-stat-icon"><i data-lucide="info" aria-hidden="true"></i></span><span class="resident-stat-label">Declined</span><strong>{{ number_format($requestCounts['declined']) }}</strong>
                </a>
            </div>
        </section>

        <div class="resident-content-grid">
            <div class="resident-activity-column">
                @if ($latestReviewedRequest)
                    <section class="resident-update @if ($latestReviewedRequest->status === 'Declined') resident-update-declined @endif" aria-labelledby="latest-update-title">
                        <div class="resident-update-heading"><span class="resident-section-icon"><i data-lucide="messages-square" aria-hidden="true"></i></span><div><h2 id="latest-update-title">Latest staff update</h2><time datetime="{{ $latestReviewedRequest->reviewed_at->toIso8601String() }}">{{ $latestReviewedRequest->reviewed_at->format('M j, Y · g:i A') }}</time></div><x-request-status :status="$latestReviewedRequest->status" /></div>
                        <h3>{{ $latestReviewedRequest->type === 'certificate' ? $latestReviewedRequest->certificate_type : 'Blotter report' }} <span>#{{ str_pad($latestReviewedRequest->id, 5, '0', STR_PAD_LEFT) }}</span></h3>
                        @if ($latestReviewedRequest->response_note)
                            <p class="resident-update-message">{{ \Illuminate\Support\Str::limit($latestReviewedRequest->response_note, 220) }}</p>
                        @elseif ($latestReviewedRequest->status === 'Completed')
                            <p>{{ $latestReviewedRequest->type === 'certificate' ? 'Your document has been issued. Contact barangay staff about collection.' : 'Your report has been added to the barangay blotter records.' }}</p>
                        @else
                            <p>Your request was declined. Contact barangay staff if you need clarification.</p>
                        @endif
                        <a href="{{ route('account.requests.show', $latestReviewedRequest) }}" class="resident-inline-link">Read request details <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                    </section>
                @endif

                <section class="resident-card resident-activity-card" aria-labelledby="recent-requests-title">
                    <div class="resident-card-heading"><div><h2 id="recent-requests-title">Recent requests</h2></div><a href="{{ route('account.requests.index') }}" class="resident-inline-link">View all requests <i data-lucide="arrow-right" aria-hidden="true"></i></a></div>
                    @if ($recentRequests->isEmpty())
                        <div class="resident-empty-state"><span class="resident-empty-icon"><i data-lucide="inbox" aria-hidden="true"></i></span><h3>Your first request starts here</h3><p>Choose a service above. Your submission and staff updates will appear in this space.</p><a href="#resident-services" class="resident-inline-link">Explore services <i data-lucide="arrow-right" aria-hidden="true"></i></a></div>
                    @else
                        <ul class="resident-request-list">
                            @foreach ($recentRequests as $serviceRequest)
                                <li><x-resident-request-item :service-request="$serviceRequest" /></li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>

            <aside class="resident-sidebar" aria-label="Account and request guidance">
                <section class="resident-card resident-account-card" aria-labelledby="account-title">
                    <div class="resident-card-heading"><div><h2 id="account-title">My account</h2></div><span class="resident-section-icon"><i data-lucide="user-round" aria-hidden="true"></i></span></div>
                    <dl class="resident-account-details"><div><dt>Email address</dt><dd>{{ $user->email }}</dd></div></dl>
                    <a href="{{ route('account.profile.edit') }}" class="resident-inline-link">Manage profile <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                </section>
                <section class="resident-card resident-guide" aria-labelledby="guide-title">
                    <h2 id="guide-title">From request to result</h2>
                    <ol class="resident-guide-steps"><li><span>1</span><div><strong>Choose a service</strong><p>Complete the form and submit your details.</p></div></li><li><span>2</span><div><strong>Staff reviews it</strong><p>Your request stays Pending until staff makes a decision.</p></div></li><li><span>3</span><div><strong>Check for an update</strong><p>Open the request for the result, staff notes, and next steps.</p></div></li></ol>
                </section>
            </aside>
        </div>
    @endif

    @if ($user->role === 'staff' || $user->resident_id === null)
        <section class="resident-card resident-account-compact" aria-labelledby="account-title"><span class="resident-section-icon"><i data-lucide="user-round" aria-hidden="true"></i></span><div><h2 id="account-title">My account</h2><p>{{ $user->name }} <span aria-hidden="true">·</span> {{ $user->email }}</p></div></section>
    @endif
@endsection
