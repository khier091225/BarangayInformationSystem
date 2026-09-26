@extends('layouts.resident')

@section('title', 'My Dashboard | Barangay Information System')

@section('content')
    <section class="resident-hero" aria-labelledby="dashboard-title">
        <div class="resident-hero-copy">
            <div class="resident-kicker"><span></span> RESIDENT PORTAL</div>
            <h1 id="dashboard-title">Welcome, {{ $user->name }}</h1>
            <p>Your barangay services and request updates, all in one place.</p>
            @if ($user->role === 'resident' && $user->resident_id !== null)
                <div class="resident-hero-actions">
                    <a href="{{ route('account.requests.certificate.create') }}" class="resident-button resident-button-light"><i data-lucide="plus" aria-hidden="true"></i> Request a document</a>
                    <a href="{{ route('account.requests.index') }}" class="resident-button resident-button-ghost">Track my requests <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                </div>
            @endif
        </div>
        <div class="resident-hero-aside" aria-label="Account status">
            <span class="resident-hero-aside-icon"><i data-lucide="{{ $user->role === 'staff' ? 'user-round' : ($user->resident_id === null ? 'info' : 'badge-check') }}" aria-hidden="true"></i></span>
            <span class="resident-hero-aside-label">ACCOUNT STATUS</span>
            <strong>{{ $user->role === 'staff' ? 'Staff access' : ($user->resident_id === null ? 'Verification needed' : 'Verified resident') }}</strong>
            <span>{{ $user->role === 'staff' ? 'Staff workspace available' : ($user->resident_id === null ? 'Enter your staff-issued code below' : 'Ready to request barangay services') }}</span>
        </div>
    </section>

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
                        <x-form.input name="registration_code" required autocomplete="off" placeholder="XXXX-XXXX-XXXX-XXXX" />
                        <x-form.error :message="$errors->first('registration_code')" />
                    </div>
                    <button type="submit" class="resident-button resident-button-primary">Verify resident record <i data-lucide="arrow-right" aria-hidden="true"></i></button>
                </form>
            </section>
            <aside class="resident-card resident-help-card" aria-labelledby="verification-help-title">
                <span class="resident-section-icon resident-section-icon-warm"><i data-lucide="info" aria-hidden="true"></i></span>
                <h2 id="verification-help-title">How verification works</h2>
                <ol class="resident-steps">
                    <li>Ask barangay staff to check your resident record.</li>
                    <li>Get the registration code issued for your record.</li>
                    <li>Enter the code here to unlock online requests.</li>
                </ol>
            </aside>
        </div>
    @else
        <section class="resident-overview" aria-labelledby="overview-title">
            <div class="resident-section-heading">
                <div>
                    <span class="resident-kicker resident-kicker-dark">YOUR ACTIVITY</span>
                    <h2 id="overview-title">At a glance</h2>
                </div>
                <p>Keep track of your submissions.</p>
            </div>
            <div class="resident-stats">
                <div class="resident-stat resident-stat-total">
                    <span class="resident-stat-icon"><i data-lucide="files" aria-hidden="true"></i></span>
                    <span class="resident-stat-label">Total requests</span>
                    <strong>{{ $requestCounts['total'] }}</strong>
                    <span class="resident-stat-note">Documents and reports submitted</span>
                </div>
                <div class="resident-stat resident-stat-pending">
                    <span class="resident-stat-icon"><i data-lucide="calendar-clock" aria-hidden="true"></i></span>
                    <span class="resident-stat-label">Under review</span>
                    <strong>{{ $requestCounts['pending'] }}</strong>
                    <span class="resident-stat-note">Awaiting staff action</span>
                </div>
                <div class="resident-stat resident-stat-completed">
                    <span class="resident-stat-icon"><i data-lucide="file-check-2" aria-hidden="true"></i></span>
                    <span class="resident-stat-label">Completed</span>
                    <strong>{{ $requestCounts['completed'] }}</strong>
                    <span class="resident-stat-note">Reviewed and processed</span>
                </div>
            </div>
        </section>

        <section class="resident-services" aria-labelledby="services-title">
            <div class="resident-section-heading">
                <div>
                    <span class="resident-kicker resident-kicker-dark">BARANGAY SERVICES</span>
                    <h2 id="services-title">How can we help?</h2>
                </div>
                <p>Choose a service to get started.</p>
            </div>
            <div class="resident-service-grid">
                <a href="{{ route('account.requests.certificate.create') }}" class="resident-service-card">
                    <span class="resident-service-icon"><i data-lucide="files" aria-hidden="true"></i></span>
                    <span class="resident-service-copy"><strong>Request a document</strong><span>Barangay Clearance, Certificate of Residency, Certificate of Indigency, or Business Clearance.</span></span>
                    <span class="resident-service-arrow"><i data-lucide="arrow-up-right" aria-hidden="true"></i></span>
                </a>
                <a href="{{ route('account.requests.blotter.create') }}" class="resident-service-card resident-service-card-alt">
                    <span class="resident-service-icon"><i data-lucide="notebook-pen" aria-hidden="true"></i></span>
                    <span class="resident-service-copy"><strong>File a blotter report</strong><span>Submit incident details for barangay staff to review and record.</span></span>
                    <span class="resident-service-arrow"><i data-lucide="arrow-up-right" aria-hidden="true"></i></span>
                </a>
            </div>
        </section>

        <div class="resident-content-grid">
            <section class="resident-card resident-activity-card" aria-labelledby="recent-requests-title">
                <div class="resident-card-heading">
                    <div>
                        <span class="resident-kicker resident-kicker-dark">RECENT ACTIVITY</span>
                        <h2 id="recent-requests-title">Recent requests</h2>
                    </div>
                    <a href="{{ route('account.requests.index') }}" class="resident-inline-link">View all <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                </div>
                @if ($recentRequests->isEmpty())
                    <div class="resident-empty-state">
                        <span class="resident-empty-icon"><i data-lucide="inbox" aria-hidden="true"></i></span>
                        <h3>No requests yet</h3>
                        <p>When you submit a document request or blotter report, its progress will appear here.</p>
                        <a href="{{ route('account.requests.certificate.create') }}" class="resident-inline-link">Make your first request <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                    </div>
                @else
                    <div class="resident-list-wrap">
                        <table class="resident-list">
                            <thead><tr><th scope="col">Request</th><th scope="col">Submitted</th><th scope="col">Status</th><th scope="col" aria-label="Details"></th></tr></thead>
                            <tbody>
                                @foreach ($recentRequests as $serviceRequest)
                                    <tr>
                                        <td><strong>{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</strong><span class="resident-table-reference">#{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                                        <td>{{ $serviceRequest->created_at->format('M d, Y') }}</td>
                                        <td><span class="resident-badge @if ($serviceRequest->status === 'Completed') resident-badge-completed @elseif ($serviceRequest->status === 'Declined') resident-badge-declined @endif">{{ $serviceRequest->status }}</span></td>
                                        <td><a href="{{ route('account.requests.show', $serviceRequest) }}" class="resident-table-link" aria-label="View request #{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}">View <i data-lucide="arrow-right" aria-hidden="true"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <aside class="resident-card resident-account-card" aria-labelledby="account-title">
                <div class="resident-card-heading">
                    <div>
                        <span class="resident-kicker resident-kicker-dark">YOUR PROFILE</span>
                        <h2 id="account-title">My account</h2>
                    </div>
                    <span class="resident-account-icon"><i data-lucide="user-round" aria-hidden="true"></i></span>
                </div>
                <dl class="resident-account-details">
                    <div><dt>Full name</dt><dd>{{ $user->name }}</dd></div>
                    <div><dt>Email address</dt><dd>{{ $user->email }}</dd></div>
                    <div><dt>Resident status</dt><dd><span class="resident-account-verified"><i data-lucide="badge-check" aria-hidden="true"></i> Verified</span></dd></div>
                </dl>
                <p class="resident-account-note">Your account is linked to a resident record verified by barangay staff.</p>
            </aside>
        </div>
    @endif

    @if ($user->role === 'staff' || $user->resident_id === null)
        <section class="resident-card resident-account-compact" aria-labelledby="account-title">
            <span class="resident-section-icon"><i data-lucide="user-round" aria-hidden="true"></i></span>
            <div><h2 id="account-title">My account</h2><p>{{ $user->name }} <span aria-hidden="true">·</span> {{ $user->email }}</p></div>
        </section>
    @endif
@endsection
