@extends('layouts.app')

@section('title', 'Resident Requests | Barangay Information System')

@section('breadcrumb')
    <span>Staff workspace</span><i data-lucide="chevron-right" aria-hidden="true"></i><strong>Resident requests</strong>
@endsection

@section('content')
    <div class="staff-requests-page">
        <x-workspace.page-header title="Resident requests" description="Review document requests and blotter reports submitted by residents." icon="inbox" />

        <nav class="staff-request-statuses" aria-label="Filter resident requests by status">
            @foreach ($statusCounts as $requestStatus => $count)
                <a href="{{ route('service-requests.index', ['status' => $requestStatus]) }}"
                    class="staff-request-status staff-request-status-{{ \Illuminate\Support\Str::slug($requestStatus) }}"
                    @if ($status === $requestStatus) aria-current="page" @endif>
                    <span class="staff-request-status-icon"><i data-lucide="{{ $requestStatus === 'Pending' ? 'clock' : ($requestStatus === 'Awaiting Payment' ? 'qr-code' : ($requestStatus === 'Completed' ? 'badge-check' : 'x')) }}" aria-hidden="true"></i></span>
                    <span class="staff-request-status-label">{{ $requestStatus }}</span>
                    <strong>{{ number_format($count) }}</strong>
                </a>
            @endforeach
        </nav>

        <section class="staff-requests-panel" aria-label="{{ $status }} resident requests">
            <div class="staff-requests-panel-heading">
                <div>
                    <h2>{{ $status }} requests</h2>
                    @if ($requests->isNotEmpty())
                        <p>Most recently submitted first</p>
                    @endif
                </div>
            </div>

            @if ($requests->isEmpty())
                <div class="staff-requests-empty">
                    <span class="staff-requests-empty-icon"><i data-lucide="{{ $status === 'Pending' ? 'badge-check' : 'inbox' }}" aria-hidden="true"></i></span>
                    @if ($requests->total() > 0)
                        <h3>No requests on this page</h3>
                        <p>Go back to the first page to see {{ strtolower($status) }} requests.</p>
                        <a href="{{ route('service-requests.index', ['status' => $status]) }}" class="staff-requests-empty-link">Back to first page <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                    @elseif ($status === 'Pending')
                        <h3>No requests waiting for review</h3>
                        <p>New requests from residents will appear here when they are submitted.</p>
                    @else
                        <h3>No {{ strtolower($status) }} requests yet</h3>
                        <p>Requests will appear here when they reach this stage.</p>
                    @endif
                </div>
            @else
                <div class="staff-requests-table-scroll">
                    <table class="workspace-table staff-requests-table">
                        <thead>
                            <tr>
                                <th scope="col">Reference</th>
                                <th scope="col">Resident</th>
                                <th scope="col">Request</th>
                                <th scope="col">Submitted</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $serviceRequest)
                                <tr>
                                    <td class="staff-request-reference">#{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="staff-request-resident">{{ $serviceRequest->resident?->full_name ?? 'Resident record unavailable' }}</td>
                                    <td class="staff-request-document">
                                        <span class="staff-request-document-icon"><i data-lucide="{{ $serviceRequest->type === 'certificate' ? 'files' : 'notebook-pen' }}" aria-hidden="true"></i></span>
                                        <span>{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</span>
                                    </td>
                                    <td class="staff-request-submitted"><time datetime="{{ $serviceRequest->created_at->toDateString() }}">{{ $serviceRequest->created_at->format('M j, Y') }}</time></td>
                                    <td class="staff-request-action"><a href="{{ route('service-requests.show', $serviceRequest) }}" class="staff-request-open">{{ $status === 'Pending' ? 'Review' : 'View details' }} <i data-lucide="arrow-right" aria-hidden="true"></i></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        @if ($requests->hasPages())
            <div class="staff-requests-pagination">{{ $requests->links() }}</div>
        @endif
    </div>
@endsection
