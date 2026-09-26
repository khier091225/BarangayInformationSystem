<div>
    <!-- Breathing in, I calm body and mind. Breathing out, I smile. - Thich Nhat Hanh -->
</div>
@extends('layouts.resident')

@section('title', 'My Requests | Barangay Information System')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 14px; flex-wrap: wrap; margin-bottom: 24px;">
        <div>
            <div class="eyebrow">REQUEST HISTORY</div>
            <h1 class="resident-heading">My requests</h1>
            <p class="resident-muted">Track document requests and blotter reports you have submitted.</p>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('account.requests.certificate.create') }}" class="button button-primary" style="text-decoration: none;">Request document</a>
            <a href="{{ route('account.requests.blotter.create') }}" class="button button-outline" style="text-decoration: none;">File blotter report</a>
        </div>
    </div>

    <div class="resident-card resident-list-wrap">
        <table class="resident-list">
            <thead><tr><th>Reference</th><th>Request</th><th>Submitted</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($requests as $serviceRequest)
                    <tr>
                        <td>#{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</td>
                        <td>{{ $serviceRequest->created_at->format('M d, Y') }}</td>
                        <td><span class="resident-badge @if ($serviceRequest->status === 'Completed') resident-badge-completed @elseif ($serviceRequest->status === 'Declined') resident-badge-declined @endif">{{ $serviceRequest->status }}</span></td>
                        <td><a href="{{ route('account.requests.show', $serviceRequest) }}">View details</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="resident-muted">No requests yet. Choose an action above to get started.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top: 20px;">{{ $requests->links() }}</div>
@endsection
