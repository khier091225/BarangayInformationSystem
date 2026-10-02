@extends('layouts.resident')

@section('title', 'My Requests | Barangay Information System')

@section('content')
    <x-workspace.page-header :title="'My '.$requestLabel" description="Follow your submissions and read updates from barangay staff." icon="inbox">
        <x-slot:actions>
            <a href="{{ route('account') }}#resident-services" class="resident-button resident-button-primary"><i data-lucide="plus" aria-hidden="true"></i> Choose a service</a>
        </x-slot:actions>
    </x-workspace.page-header>

    <section class="resident-card resident-history-card" aria-label="Submitted requests">
        <nav class="resident-history-type-filters" aria-label="Filter requests by type">
            <a href="{{ route('account.requests.index', array_filter(['status' => $status])) }}" @if ($type === null) aria-current="page" @endif>All requests</a>
            <a href="{{ route('account.requests.index', array_filter(['type' => 'certificate', 'status' => $status])) }}" @if ($type === 'certificate') aria-current="page" @endif>Documents</a>
            <a href="{{ route('account.requests.index', array_filter(['type' => 'blotter', 'status' => $status])) }}" @if ($type === 'blotter') aria-current="page" @endif>Blotters</a>
        </nav>
        <nav class="resident-history-filters" aria-label="Filter requests by status">
            <a href="{{ route('account.requests.index', array_filter(['type' => $type])) }}" @if ($status === null) aria-current="page" @endif>All statuses</a>
            @foreach (['Pending', 'Awaiting Payment', 'Completed', 'Declined'] as $requestStatus)
                <a href="{{ route('account.requests.index', array_filter(['type' => $type, 'status' => $requestStatus])) }}" @if ($status === $requestStatus) aria-current="page" @endif>{{ $requestStatus }}</a>
            @endforeach
        </nav>
        <div class="resident-history-summary"><span>@if ($requests->isEmpty()){{ number_format($requests->total()) }} {{ \Illuminate\Support\Str::plural('request', $requests->total()) }}@else Showing {{ $requests->firstItem() }}–{{ $requests->lastItem() }} of {{ number_format($requests->total()) }} {{ \Illuminate\Support\Str::plural('request', $requests->total()) }} @endif</span><span>Most recently updated first</span></div>
        @if ($requests->isEmpty())
            <div class="resident-empty-state resident-history-empty">
                <span class="resident-empty-icon"><i data-lucide="inbox" aria-hidden="true"></i></span>
                @if ($requests->total() > 0)
                    <h2>No requests on this page</h2><a href="{{ route('account.requests.index', array_filter(['type' => $type, 'status' => $status])) }}" class="resident-inline-link">Back to first page <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                @elseif ($status)
                    <h2>No {{ strtolower($status) }} {{ $requestLabel }}</h2><a href="{{ route('account.requests.index', array_filter(['type' => $type])) }}" class="resident-inline-link">View all statuses <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                @else
                    <h2>No {{ $requestLabel }} yet</h2><p>New submissions will appear here after you send them.</p><a href="{{ route('account') }}#resident-services" class="resident-inline-link">Explore services <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                @endif
            </div>
        @else
            <ul class="resident-request-list">
                @foreach ($requests as $serviceRequest)
                    <li><x-resident-request-item :service-request="$serviceRequest" /></li>
                @endforeach
            </ul>
        @endif
    </section>
    <div class="resident-pagination">{{ $requests->links() }}</div>
@endsection
