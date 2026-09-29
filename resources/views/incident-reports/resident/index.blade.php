@extends('layouts.resident')

@section('title', 'My Incident Reports | Barangay Information System')

@section('content')
    <div class="resident-page-heading">
        <div><span class="resident-kicker resident-kicker-dark">ONLINE SUMBONG</span><h1>My reports</h1><p>Keep your reference number handy and check here for staff updates.</p></div>
        <a href="{{ route('account.incidents.create') }}" class="resident-button resident-button-primary"><i data-lucide="message-square-plus" aria-hidden="true"></i> Report an incident</a>
    </div>
    <section class="resident-card incident-list-card" aria-label="Your incident reports">
        <nav class="resident-history-filters" aria-label="Filter reports by status">
            <a href="{{ route('account.incidents.index') }}" @if ($status === null) aria-current="page" @endif>All reports</a>
            <a href="{{ route('account.incidents.index', ['status' => 'Active']) }}" @if ($status === 'Active') aria-current="page" @endif>In progress</a>
            @foreach (['Submitted', 'Assigned', 'Responding', 'Resolved', 'Closed'] as $option)
                <a href="{{ route('account.incidents.index', ['status' => $option]) }}" @if ($status === $option) aria-current="page" @endif>{{ $option }}</a>
            @endforeach
        </nav>
        <div class="resident-history-summary"><span>@if ($reports->isEmpty()){{ number_format($reports->total()) }} {{ \Illuminate\Support\Str::plural('report', $reports->total()) }}@else Showing {{ $reports->firstItem() }}–{{ $reports->lastItem() }} of {{ number_format($reports->total()) }} {{ \Illuminate\Support\Str::plural('report', $reports->total()) }} @endif</span><span>Most recently updated first</span></div>
        @forelse ($reports as $report)
            <a class="incident-list-row" href="{{ route('account.incidents.show', $report) }}">
                <span class="resident-section-icon"><i data-lucide="message-square-warning" aria-hidden="true"></i></span>
                <span class="incident-list-copy"><small>{{ $report->reference_number }} · {{ $report->created_at->timezone('Asia/Manila')->format('M j, Y') }}</small><strong>{{ $report->categoryLabel() }}</strong><span>{{ $report->location }}</span></span>
                <x-incident-status :status="$report->status" />
                <span class="incident-list-open"><span>View details</span><i data-lucide="arrow-right" aria-hidden="true"></i></span>
            </a>
        @empty
            <div class="resident-empty-state incident-list-empty">
                <span class="resident-empty-icon"><i data-lucide="messages-square" aria-hidden="true"></i></span>
                @if ($reports->total() > 0)
                    <h2>No reports on this page</h2><a class="resident-inline-link" href="{{ route('account.incidents.index', ['status' => $status]) }}">Back to first page <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                @elseif ($status)
                    <h2>No {{ strtolower($status) }} reports</h2><p>Try another status or view all of your reports.</p><a class="resident-inline-link" href="{{ route('account.incidents.index') }}">View all reports <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                @else
                    <h2>No reports yet</h2><p>When you submit an incident report, its progress will appear here.</p><a class="resident-inline-link" href="{{ route('account.incidents.create') }}">Report an incident <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                @endif
            </div>
        @endforelse
    </section>
    @if ($reports->hasPages())<div class="resident-pagination">{{ $reports->links() }}</div>@endif
@endsection
