@extends('layouts.app')

@section('title', 'Incident Reports | Barangay Information System')

@section('breadcrumb')
    <span class="incident-index-breadcrumb">Case management</span><i data-lucide="chevron-right" aria-hidden="true"></i><strong>Incident reports</strong>
@endsection

@section('content')
    <div class="staff-requests-page">
        <header class="staff-requests-heading">
            <div><h1>Incident reports</h1><p>Review incident reports submitted online, accept reports, and keep residents informed as work progresses.</p></div>
            <div class="incident-heading-actions"><a href="{{ route('incident-report-contacts.index') }}" class="button button-primary"><i data-lucide="bell" aria-hidden="true"></i> Alert contacts</a></div>
        </header>
        <nav class="incident-filter-nav incident-staff-tabs" aria-label="Filter incident reports by status">
            @foreach (['Submitted', 'Assigned', 'Responding', 'Resolved', 'Closed'] as $option)
                <a href="{{ route('incident-reports.index', ['status' => $option, 'team' => $team, 'search' => $search]) }}" @if ($status === $option) aria-current="page" @endif>{{ $option }} <span>{{ number_format($statusCounts[$option] ?? 0) }}</span></a>
            @endforeach
        </nav>
        <form method="GET" action="{{ route('incident-reports.index') }}" class="incident-filter-form" role="search">
            <input type="hidden" name="status" value="{{ $status }}">
            <label for="search">Search reference or location<input id="search" name="search" type="search" class="form-control" value="{{ $search }}" placeholder="INC-2026-000123 or Purok 3" maxlength="255"></label>
            <label for="team">Suggested team<select id="team" name="team" class="form-control"><option value="">All teams</option>@foreach (\App\Models\IncidentReport::TEAM_LABELS as $value => $label)<option value="{{ $value }}" @selected($team === $value)>{{ $label }}</option>@endforeach</select></label>
            <button type="submit" class="button button-primary"><i data-lucide="search" aria-hidden="true"></i> Apply filters</button>
        </form>
        <section class="staff-requests-panel" aria-label="{{ $status }} incident reports">
            <div class="staff-requests-panel-heading"><h2>{{ $status }} reports</h2><p>{{ $status === 'Submitted' ? 'Oldest reports appear first.' : 'Follow up on current and completed work.' }}</p></div>
            @if ($reports->isEmpty())
                <div class="staff-requests-empty"><span class="staff-requests-empty-icon"><i data-lucide="inbox" aria-hidden="true"></i></span><h3>No matching reports</h3><p>Try a different status, team, or search term.</p></div>
            @else
                <div class="staff-requests-table-scroll">
                    <table class="workspace-table incident-table">
                        <thead><tr><th scope="col">Reference</th><th scope="col">Category and location</th><th scope="col">Suggested team</th><th scope="col">Resident</th><th scope="col">Submitted</th><th scope="col">Action</th></tr></thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td data-label="Reference"><strong>{{ $report->reference_number }}</strong></td>
                                    <td data-label="Report"><strong>{{ $report->categoryLabel() }}</strong><small>{{ $report->location }}</small></td>
                                    <td data-label="Team">{{ $report->suggestedTeamLabel() }}</td>
                                    <td data-label="Resident">{{ $report->keep_identity_confidential ? 'Confidential' : ($report->resident?->full_name ?? 'Resident record unavailable') }}</td>
                                    <td data-label="Submitted"><time datetime="{{ $report->created_at->toIso8601String() }}">{{ $report->created_at->timezone('Asia/Manila')->format('M j, Y') }}</time></td>
                                    <td data-label="Action"><a class="staff-request-open" href="{{ route('incident-reports.show', $report) }}">{{ $status === 'Submitted' ? 'Review' : 'View' }} <i data-lucide="arrow-right" aria-hidden="true"></i></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
        @if ($reports->hasPages())<div class="staff-requests-pagination">{{ $reports->links() }}</div>@endif
    </div>
@endsection
