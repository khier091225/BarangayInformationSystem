@extends('layouts.app')

@section('title', 'Dashboard | Barangay Information System')

@section('breadcrumb')
    <span>Staff workspace</span>
    <i data-lucide="chevron-right" aria-hidden="true"></i>
    <strong>Overview</strong>
@endsection

@section('content')
    <div class="staff-overview">
        <header class="overview-heading">
            <div>
                <h1>Barangay overview</h1>
                <p>A clear view of your community, services, and work awaiting review.</p>
            </div>
            <div class="overview-heading-actions">
                <span class="overview-date"><i data-lucide="calendar-days" aria-hidden="true"></i> {{ today()->format('D, M j, Y') }}</span>
                <a href="{{ route('residents.create') }}" class="overview-button overview-button-primary"><i data-lucide="user-round-plus" aria-hidden="true"></i> Add resident</a>
            </div>
        </header>

        <div class="overview-toolbar">
            <form method="GET" action="{{ route('residents.index') }}" class="overview-search" role="search">
                <i data-lucide="search" aria-hidden="true"></i>
                <input type="search" name="search" placeholder="Find a resident by name, address, or contact" aria-label="Search resident records" maxlength="255">
                <button type="submit">Search <i data-lucide="arrow-right" aria-hidden="true"></i></button>
            </form>
            <div class="overview-shortcuts">
                <a href="{{ route('certificates.create') }}"><i data-lucide="file-check-2" aria-hidden="true"></i> Issue certificate</a>
                <a href="{{ route('blotters.create') }}"><i data-lucide="notebook-pen" aria-hidden="true"></i> Record blotter</a>
            </div>
        </div>

        <section class="overview-attention" aria-labelledby="attention-title">
            <div class="overview-attention-intro">
                <span class="overview-attention-icon"><i data-lucide="inbox" aria-hidden="true"></i></span>
                <div><h2 id="attention-title">{{ $pendingServiceRequestCount + $pendingBlotterCount + $pendingIncidentReportCount > 0 ? 'Needs your attention' : 'You’re all caught up' }}</h2><p>{{ $pendingServiceRequestCount + $pendingBlotterCount + $pendingIncidentReportCount > 0 ? 'Start with the requests and cases waiting for staff action.' : 'There are no pending resident requests, incident reports, or blotter cases.' }}</p></div>
            </div>
            <div class="overview-attention-links">
                <a href="{{ route('service-requests.index', ['status' => 'Pending']) }}"><strong>{{ number_format($pendingServiceRequestCount) }}</strong><span>Pending requests</span><i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                <a href="{{ route('incident-reports.index', ['status' => 'Submitted']) }}"><strong>{{ number_format($pendingIncidentReportCount) }}</strong><span>New incident reports</span><i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                <a href="{{ route('blotters.index', ['status' => 'Pending']) }}"><strong>{{ number_format($pendingBlotterCount) }}</strong><span>Pending blotters</span><i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
            </div>
        </section>

        <section class="overview-stats" aria-label="Barangay record totals">
            <x-dashboard-stat label="Total residents" :value="$residentCount" icon="users-round" :href="route('residents.index')" />
            <x-dashboard-stat label="Households" :value="$householdCount" icon="house" :href="route('households.index')" tone="blue" />
            <x-dashboard-stat label="Certificates" :value="$certificateCount" icon="files" :href="route('certificates.index')" tone="violet" />
            <x-dashboard-stat label="Barangay officials" :value="$officialCount" icon="badge-check" :href="route('officials.index')" tone="amber" />
        </section>

        <div class="overview-main-grid">
            <section class="overview-panel overview-chart-panel" aria-labelledby="certificate-activity-title">
                <div class="overview-panel-heading">
                    <div><h2 id="certificate-activity-title">Certificates issued</h2><p>Monthly volume for the last six months</p></div>
                    <span class="overview-panel-icon"><i data-lucide="chart-no-axes-combined" aria-hidden="true"></i></span>
                </div>
                <div class="overview-chart-summary">
                    <div><strong>{{ number_format($certificateActivity['total']) }}</strong><span>issued in this period</span></div>
                    <span class="overview-chart-current"><span></span> {{ number_format($certificateActivity['currentMonth']) }} this month</span>
                </div>
                <figure class="overview-chart" aria-labelledby="certificate-activity-title certificate-chart-caption">
                    <div class="overview-chart-axis" aria-hidden="true"><span>{{ number_format($certificateActivity['maximum']) }}</span><span>{{ number_format($certificateActivity['maximum'] / 2) }}</span><span>0</span></div>
                    <ol class="overview-chart-plot">
                        @foreach ($certificateActivity['months'] as $month)
                            <li class="overview-chart-column @if ($loop->last) overview-chart-column-current @endif" aria-label="{{ $month['fullLabel'] }}: {{ $month['count'] }} certificates issued">
                                <div class="overview-chart-bar-space" aria-hidden="true"><div class="overview-chart-bar" style="height: {{ $month['count'] / $certificateActivity['maximum'] * 100 }}%;"><span>{{ number_format($month['count']) }}</span></div></div>
                                <span class="overview-chart-month" aria-hidden="true">{{ $month['label'] }}</span>
                            </li>
                        @endforeach
                    </ol>
                    <figcaption id="certificate-chart-caption">{{ $certificateActivity['months'][0]['fullLabel'] }} – {{ $certificateActivity['months'][5]['fullLabel'] }}. {{ $certificateActivity['total'] === 0 ? 'No certificates issued in this period.' : 'Based on issuance dates, through today.' }}</figcaption>
                </figure>
                <a href="{{ route('certificates.index') }}" class="overview-panel-footer">Open certificate records <i data-lucide="arrow-right" aria-hidden="true"></i></a>
            </section>

            <section class="overview-panel overview-queue" id="review-queue" aria-labelledby="review-queue-title">
                <div class="overview-panel-heading">
                    <div><h2 id="review-queue-title">Request review queue <span class="overview-count">{{ number_format($pendingServiceRequestCount) }}</span></h2><p>Oldest requests appear first</p></div>
                </div>
                <div class="overview-queue-list">
                    @forelse ($pendingRequests as $serviceRequest)
                        <a class="overview-queue-item" href="{{ route('service-requests.show', $serviceRequest) }}">
                            <span class="overview-list-icon @if ($serviceRequest->type === 'blotter') overview-list-icon-amber @endif"><i data-lucide="{{ $serviceRequest->type === 'certificate' ? 'files' : 'notebook-pen' }}" aria-hidden="true"></i></span>
                            <span class="overview-queue-copy"><strong>{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</strong><span>{{ $serviceRequest->resident?->full_name ?? 'Resident record unavailable' }}</span><small>#{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }} · {{ $serviceRequest->created_at->format('M j, Y') }}</small></span>
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                        </a>
                    @empty
                        <div class="overview-empty"><span><i data-lucide="badge-check" aria-hidden="true"></i></span><h3>No requests waiting</h3></div>
                    @endforelse
                </div>
                @if ($pendingServiceRequestCount > 0)
                    <a href="{{ route('service-requests.index', ['status' => 'Pending']) }}" class="overview-panel-footer">View pending requests <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                @endif
            </section>
        </div>

        <div class="overview-secondary-grid">
            <section class="overview-panel" aria-labelledby="request-status-title">
                <div class="overview-panel-heading"><div><h2 id="request-status-title">Resident requests</h2><p>All submissions, by current status</p></div></div>
                <div class="overview-request-total"><strong>{{ number_format($totalServiceRequestCount) }}</strong><span>total requests</span></div>
                <div class="overview-status-meter" aria-hidden="true">
                    @foreach ($requestCounts as $status => $count)
                        <span class="overview-status-{{ strtolower($status) }}" style="width: {{ $totalServiceRequestCount > 0 ? $count / $totalServiceRequestCount * 100 : 0 }}%;"></span>
                    @endforeach
                </div>
                <ul class="overview-status-list">
                    @foreach ($requestCounts as $status => $count)
                        <li><a href="{{ route('service-requests.index', ['status' => $status]) }}"><span class="overview-status-dot overview-status-{{ strtolower($status) }}" aria-hidden="true"></span><span>{{ $status }}</span><strong>{{ number_format($count) }}</strong><i data-lucide="chevron-right" aria-hidden="true"></i></a></li>
                    @endforeach
                </ul>
            </section>

            <section class="overview-panel overview-recent" aria-labelledby="recent-certificates-title">
                <div class="overview-panel-heading"><div><h2 id="recent-certificates-title">Recently issued certificates</h2></div></div>
                <div class="overview-record-list">
                    @forelse ($recentCertificates as $certificate)
                        <a href="{{ route('certificates.show', $certificate) }}" class="overview-record-item">
                            <span><strong>{{ $certificate->resident?->full_name ?? 'Resident record unavailable' }}</strong><span>{{ $certificate->certificate_type }}</span><small>Issued {{ $certificate->date_issued->format('M j, Y') }}</small></span><i data-lucide="arrow-up-right" aria-hidden="true"></i>
                        </a>
                    @empty
                        <div class="overview-empty overview-empty-small"><span><i data-lucide="files" aria-hidden="true"></i></span><p>No certificates issued yet.</p><a href="{{ route('certificates.create') }}">Issue a certificate <i data-lucide="arrow-right" aria-hidden="true"></i></a></div>
                    @endforelse
                </div>
                <a href="{{ route('certificates.index') }}" class="overview-panel-footer">View certificates <i data-lucide="arrow-right" aria-hidden="true"></i></a>
            </section>

            <section class="overview-panel overview-recent" aria-labelledby="recent-blotters-title">
                <div class="overview-panel-heading"><div><h2 id="recent-blotters-title">Recent blotter cases</h2></div></div>
                <div class="overview-record-list">
                    @forelse ($recentBlotters as $blotter)
                        <a href="{{ route('blotters.show', $blotter) }}" class="overview-record-item">
                            <span><strong>{{ $blotter->complainant }}</strong><span>Respondent: {{ $blotter->respondent }}</span><small>{{ $blotter->incident_date->format('M j, Y') }} <span class="overview-case-status overview-case-{{ strtolower($blotter->status) }}">{{ $blotter->status }}</span></small></span><i data-lucide="arrow-up-right" aria-hidden="true"></i>
                        </a>
                    @empty
                        <div class="overview-empty overview-empty-small"><span><i data-lucide="notebook-pen" aria-hidden="true"></i></span><p>No blotter records yet.</p><a href="{{ route('blotters.create') }}">Record a blotter <i data-lucide="arrow-right" aria-hidden="true"></i></a></div>
                    @endforelse
                </div>
                <a href="{{ route('blotters.index') }}" class="overview-panel-footer">View all {{ number_format($totalBlotterCount) }} cases <i data-lucide="arrow-right" aria-hidden="true"></i></a>
            </section>
        </div>

        <section class="overview-panel overview-residents" aria-labelledby="recent-residents-title">
            <div class="overview-panel-heading"><div><h2 id="recent-residents-title">Newly registered residents</h2></div><a href="{{ route('residents.index') }}" class="overview-text-link">View directory <i data-lucide="arrow-right" aria-hidden="true"></i></a></div>
            <div class="overview-resident-list">
                @forelse ($recentResidents as $resident)
                    <a href="{{ route('residents.show', $resident) }}" class="overview-resident-item"><span class="overview-list-icon"><i data-lucide="user-round" aria-hidden="true"></i></span><span><strong>{{ $resident->full_name }}</strong><small>{{ $resident->household?->household_number ?? 'No household assigned' }}</small></span><i data-lucide="chevron-right" aria-hidden="true"></i></a>
                @empty
                    <p class="overview-empty-inline">No resident records yet. Add a resident to start building the directory.</p>
                @endforelse
            </div>
        </section>
        <footer class="overview-footer"><span>Barangay Information System</span><span>Records and totals as of {{ now()->format('M j, Y · g:i A') }}</span></footer>
    </div>
@endsection
