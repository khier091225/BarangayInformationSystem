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
            <div class="resident-section-heading"><h2 id="services-title">Start here</h2></div>
            <div class="resident-service-grid">
                <a href="{{ route('account.requests.certificate.create') }}" class="resident-service-card resident-service-documents">
                    <span class="resident-service-icon"><i data-lucide="files" aria-hidden="true"></i></span>
                    <span class="resident-service-copy"><strong>Request a document</strong><small>Clearance or certificates</small></span>
                    <i data-lucide="arrow-up-right" aria-hidden="true"></i>
                </a>
                <a href="{{ route('account.requests.blotter.create') }}" class="resident-service-card resident-service-report">
                    <span class="resident-service-icon"><i data-lucide="notebook-pen" aria-hidden="true"></i></span>
                    <span class="resident-service-copy"><strong>File a blotter report</strong><small>Request an official incident record</small></span>
                    <i data-lucide="arrow-up-right" aria-hidden="true"></i>
                </a>
                <a href="{{ route('account.incidents.create') }}" class="resident-service-card resident-service-incident">
                    <span class="resident-service-icon"><i data-lucide="message-square-warning" aria-hidden="true"></i></span>
                    <span class="resident-service-copy"><strong>Report an incident</strong><small>Alert the barangay about a concern</small></span>
                    <i data-lucide="arrow-up-right" aria-hidden="true"></i>
                </a>
            </div>
        </section>

        <nav class="resident-request-summary" aria-label="Request status filters">
            <a href="{{ route('account.requests.index') }}" class="resident-request-count"><span>Total requests</span><strong>{{ number_format($requestCounts['total']) }}</strong></a>
            <a href="{{ route('account.requests.index', ['status' => 'Pending']) }}" class="resident-request-count resident-request-count-pending"><span>Pending</span><strong>{{ number_format($requestCounts['pending']) }}</strong></a>
            <a href="{{ route('account.requests.index', ['status' => 'Completed']) }}" class="resident-request-count resident-request-count-completed"><span>Completed</span><strong>{{ number_format($requestCounts['completed']) }}</strong></a>
            <a href="{{ route('account.requests.index', ['status' => 'Declined']) }}" class="resident-request-count resident-request-count-declined"><span>Declined</span><strong>{{ number_format($requestCounts['declined']) }}</strong></a>
        </nav>

        <section class="resident-card resident-dashboard-activity" aria-labelledby="activity-title">
            <div class="resident-card-heading resident-dashboard-activity-heading">
                <div><h2 id="activity-title">Recent activity</h2><p>Check your requests, reports, and staff updates.</p></div>
                <div class="resident-dashboard-activity-links">
                    <a href="{{ route('account.requests.index') }}" class="resident-inline-link">My requests <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                    <a href="{{ route('account.incidents.index') }}" class="resident-inline-link">My reports <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>

            @if ($latestReviewedRequest)
                <a href="{{ route('account.requests.show', $latestReviewedRequest) }}" class="resident-dashboard-highlight">
                    <span class="resident-section-icon"><i data-lucide="messages-square" aria-hidden="true"></i></span>
                    <span class="resident-dashboard-highlight-copy">
                        <span class="resident-dashboard-eyebrow">STAFF UPDATE · {{ $latestReviewedRequest->reviewed_at->format('M j, Y') }}</span>
                        <strong>{{ $latestReviewedRequest->type === 'certificate' ? $latestReviewedRequest->certificate_type : 'Blotter report' }}</strong>
                        <small>{{ \Illuminate\Support\Str::limit($latestReviewedRequest->response_note ?: ($latestReviewedRequest->status === 'Completed' ? 'Request completed. Open it for the next steps.' : 'Request declined. Open it for details.'), 130) }}</small>
                    </span>
                    <x-request-status :status="$latestReviewedRequest->status" />
                    <i data-lucide="arrow-right" aria-hidden="true"></i>
                </a>
            @endif

            @if ($latestIncidentReport)
                <a href="{{ route('account.incidents.show', $latestIncidentReport) }}" class="resident-dashboard-highlight">
                    <span class="resident-section-icon"><i data-lucide="message-square-warning" aria-hidden="true"></i></span>
                    <span class="resident-dashboard-highlight-copy">
                        <span class="resident-dashboard-eyebrow">INCIDENT REPORT · {{ $latestIncidentReport->reference_number }}</span>
                        <strong>{{ $latestIncidentReport->categoryLabel() }}</strong>
                        <small>Updated {{ $latestIncidentReport->updated_at->format('M j, Y') }}</small>
                    </span>
                    <x-incident-status :status="$latestIncidentReport->status" />
                    <i data-lucide="arrow-right" aria-hidden="true"></i>
                </a>
            @endif

            @if ($recentRequests->isNotEmpty())
                <h3 class="resident-dashboard-list-title">Recent requests</h3>
                <ul class="resident-request-list">
                    @foreach ($recentRequests as $serviceRequest)
                        <li><x-resident-request-item :service-request="$serviceRequest" /></li>
                    @endforeach
                </ul>
            @elseif (! $latestReviewedRequest && ! $latestIncidentReport)
                <p class="resident-dashboard-empty">Nothing submitted yet. Choose a service above to get started.</p>
            @endif
        </section>
    @endif

    @if ($user->role === 'staff' || $user->resident_id === null)
        <section class="resident-card resident-account-compact" aria-labelledby="account-title"><span class="resident-section-icon"><i data-lucide="user-round" aria-hidden="true"></i></span><div><h2 id="account-title">My account</h2><p>{{ $user->name }} <span aria-hidden="true">·</span> {{ $user->email }}</p></div></section>
    @endif
@endsection
