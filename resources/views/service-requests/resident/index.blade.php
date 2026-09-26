@extends('layouts.resident')

@section('title', 'My Requests | Barangay Information System')

@section('content')
    <div class="resident-page-heading">
        <div>
            <span class="resident-kicker resident-kicker-dark">REQUEST HISTORY</span>
            <h1>My requests</h1>
            <p>Track the status of your document requests and blotter reports.</p>
        </div>
        <div class="resident-page-actions">
            <a href="{{ route('account.requests.certificate.create') }}" class="resident-button resident-button-primary"><i data-lucide="plus" aria-hidden="true"></i> Request document</a>
            <a href="{{ route('account.requests.blotter.create') }}" class="resident-button resident-button-outline">File blotter report</a>
        </div>
    </div>

    <section class="resident-card resident-history-card" aria-label="Submitted requests">
        @if ($requests->isEmpty())
            <div class="resident-empty-state resident-history-empty">
                <span class="resident-empty-icon"><i data-lucide="inbox" aria-hidden="true"></i></span>
                <h2>No requests yet</h2>
                <p>Your requests will appear here once submitted. Choose a service above to get started.</p>
            </div>
        @else
            <div class="resident-list-wrap">
                <table class="resident-list">
                    <thead><tr><th scope="col">Reference</th><th scope="col">Request</th><th scope="col">Submitted</th><th scope="col">Status</th><th scope="col" aria-label="Details"></th></tr></thead>
                    <tbody>
                        @foreach ($requests as $serviceRequest)
                            <tr>
                                <td>#{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td><strong>{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</strong></td>
                                <td>{{ $serviceRequest->created_at->format('M d, Y') }}</td>
                                <td><span class="resident-badge @if ($serviceRequest->status === 'Completed') resident-badge-completed @elseif ($serviceRequest->status === 'Declined') resident-badge-declined @endif">{{ $serviceRequest->status }}</span></td>
                                <td><a href="{{ route('account.requests.show', $serviceRequest) }}" class="resident-table-link">View details <i data-lucide="arrow-right" aria-hidden="true"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
    <div class="resident-pagination">{{ $requests->links() }}</div>
@endsection
