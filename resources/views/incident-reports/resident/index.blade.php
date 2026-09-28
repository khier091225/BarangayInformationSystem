@extends('layouts.resident')

@section('title', 'My Incident Reports | Barangay Information System')

@section('content')
    <div class="resident-page-heading">
        <div><span class="resident-kicker resident-kicker-dark">ONLINE SUMBONG</span><h1>My incident reports</h1><p>Keep your reference number handy and check here for staff updates.</p></div>
        <a href="{{ route('account.incidents.create') }}" class="resident-button resident-button-primary"><i data-lucide="message-square-plus" aria-hidden="true"></i> Report an incident</a>
    </div>
    <section class="resident-card incident-list-card" aria-label="Your incident reports">
        <nav class="incident-filter-nav" aria-label="Filter reports by status">
            <a href="{{ route('account.incidents.index') }}" @if ($status === null) aria-current="page" @endif>All</a>
            @foreach (['Submitted', 'Assigned', 'Responding', 'Resolved', 'Closed'] as $option)
                <a href="{{ route('account.incidents.index', ['status' => $option]) }}" @if ($status === $option) aria-current="page" @endif>{{ $option }}</a>
            @endforeach
        </nav>
        @forelse ($reports as $report)
            <a class="incident-list-row" href="{{ route('account.incidents.show', $report) }}">
                <span class="resident-section-icon"><i data-lucide="message-square-warning" aria-hidden="true"></i></span>
                <span class="incident-list-copy"><small>{{ $report->reference_number }} · {{ $report->created_at->timezone('Asia/Manila')->format('M j, Y') }}</small><strong>{{ $report->categoryLabel() }}</strong><span>{{ $report->location }}</span></span>
                <x-incident-status :status="$report->status" />
                <i data-lucide="chevron-right" aria-hidden="true"></i>
            </a>
        @empty
            <div class="resident-empty-state incident-list-empty"><span class="resident-empty-icon"><i data-lucide="messages-square" aria-hidden="true"></i></span><h2>No reports here yet</h2><p>{{ $status ? 'Try another status, or view all of your reports.' : 'When you submit an incident report, its progress will appear here.' }}</p><a class="resident-inline-link" href="{{ $status ? route('account.incidents.index') : route('account.incidents.create') }}">{{ $status ? 'View all reports' : 'Report an incident' }} <i data-lucide="arrow-right" aria-hidden="true"></i></a></div>
        @endforelse
    </section>
    @if ($reports->hasPages())<div class="resident-pagination">{{ $reports->links() }}</div>@endif
@endsection
