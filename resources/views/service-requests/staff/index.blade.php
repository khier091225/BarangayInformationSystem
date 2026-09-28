@extends('layouts.app')

@section('title', 'Resident Requests | Barangay Information System')

@section('breadcrumb')
    <span>Workspace</span><i data-lucide="chevron-right"></i><strong>Resident requests</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 26px; color: var(--ink); margin: 4px 0 8px;">Resident requests</h1>
        <p style="color: var(--muted-soft); font-size: 13px;">Review certificate requests and blotter reports before creating official records.</p>
    </div>

    <form method="GET" action="{{ route('service-requests.index') }}" class="search-filter-card" style="margin-bottom: 20px;">
        <label for="status" style="font-size: 13px; font-weight: 600; margin-right: 8px;">Status</label>
        <select id="status" name="status" class="search-select">
            @foreach (['Pending', 'Completed', 'Declined'] as $option)
                <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
            @endforeach
        </select>
        <button type="submit" class="search-button-primary">Apply</button>
    </form>

    <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; overflow-x: auto;">
        <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead><tr style="background: var(--canvas); text-align: left;">
                <th style="padding: 14px;">Reference</th><th style="padding: 14px;">Resident</th><th style="padding: 14px;">Request</th><th style="padding: 14px;">Submitted</th><th style="padding: 14px;">Action</th>
            </tr></thead>
            <tbody>
                @forelse ($requests as $serviceRequest)
                    <tr style="border-top: 1px solid var(--line);">
                        <td style="padding: 14px;">#{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td style="padding: 14px; font-weight: 600;">{{ $serviceRequest->resident->full_name }}</td>
                        <td style="padding: 14px;">{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</td>
                        <td style="padding: 14px;">{{ $serviceRequest->created_at->format('M d, Y') }}</td>
                        <td style="padding: 14px;"><a href="{{ route('service-requests.show', $serviceRequest) }}" class="table-action-link">Review</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding: 30px; text-align: center; color: var(--muted-soft);">No {{ strtolower($status) }} requests.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top: 20px;">{{ $requests->links() }}</div>
@endsection
