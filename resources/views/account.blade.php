@extends('layouts.resident')

@section('title', 'My Dashboard | Barangay Information System')

@section('content')
    <div style="margin-bottom: 24px;">
        <div class="eyebrow">COMMUNITY PORTAL</div>
        <h1 class="resident-heading">Welcome, {{ $user->name }}</h1>
        <p class="resident-muted" style="margin: 0;">View your requests and barangay account in one place.</p>
    </div>

    @if ($user->role === 'staff')
        <div class="resident-card">
            <h2 style="margin-top: 0;">Staff account</h2>
            <p class="resident-muted">Open the staff workspace to manage barangay records and resident requests.</p>
            <a href="{{ route('dashboard') }}" class="button button-primary" style="display: inline-block; text-decoration: none;">Open staff workspace</a>
        </div>
    @elseif ($user->resident_id === null)
        <div class="resident-card" style="max-width: 560px;">
            <h2 style="margin-top: 0;">Verify your resident record</h2>
            <p class="resident-muted">Your account is not linked to a verified resident record. Ask barangay staff for a registration code before submitting requests.</p>
            <form method="POST" action="{{ route('account.verify') }}">
                @csrf
                <div style="margin: 18px 0;">
                    <x-form.label for="registration_code" required>Registration code</x-form.label>
                    <x-form.input name="registration_code" required autocomplete="off" placeholder="XXXX-XXXX-XXXX-XXXX" />
                    <x-form.error :message="$errors->first('registration_code')" />
                </div>
                <button type="submit" class="button button-primary">Verify resident record</button>
            </form>
        </div>
    @else
        <div class="resident-grid" style="margin-bottom: 26px;">
            <div class="resident-card"><div class="resident-muted">Total requests</div><strong style="font-size: 30px; color: #1e5236;">{{ $requestCounts['total'] }}</strong></div>
            <div class="resident-card"><div class="resident-muted">Under review</div><strong style="font-size: 30px; color: #845f14;">{{ $requestCounts['pending'] }}</strong></div>
            <div class="resident-card"><div class="resident-muted">Completed</div><strong style="font-size: 30px; color: #236339;">{{ $requestCounts['completed'] }}</strong></div>
        </div>

        <h2 style="font-size: 20px; color: #1e3a29; margin: 0 0 14px;">What would you like to do?</h2>
        <div class="resident-grid" style="margin-bottom: 28px;">
            <div class="resident-card">
                <h3 style="margin: 0 0 8px; color: #1e3a29;">Request a document</h3>
                <p class="resident-muted">Barangay Clearance, Certificate of Residency, Certificate of Indigency, or Business Clearance.</p>
                <a href="{{ route('account.requests.certificate.create') }}" class="button button-primary" style="display: inline-block; text-decoration: none; margin-top: 8px;">Request document</a>
            </div>
            <div class="resident-card">
                <h3 style="margin: 0 0 8px; color: #1e3a29;">File a blotter report</h3>
                <p class="resident-muted">Submit incident details for barangay staff to review and record.</p>
                <a href="{{ route('account.requests.blotter.create') }}" class="button button-outline" style="display: inline-block; text-decoration: none; margin-top: 8px;">File a report</a>
            </div>
        </div>

        <div class="resident-card">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 10px;">
                <h2 style="font-size: 20px; color: #1e3a29; margin: 0;">Recent requests</h2>
                <a href="{{ route('account.requests.index') }}">View all requests</a>
            </div>
            <div class="resident-list-wrap">
                <table class="resident-list">
                    <thead><tr><th>Request</th><th>Submitted</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($recentRequests as $serviceRequest)
                            <tr>
                                <td>{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</td>
                                <td>{{ $serviceRequest->created_at->format('M d, Y') }}</td>
                                <td><span class="resident-badge @if ($serviceRequest->status === 'Completed') resident-badge-completed @elseif ($serviceRequest->status === 'Declined') resident-badge-declined @endif">{{ $serviceRequest->status }}</span></td>
                                <td><a href="{{ route('account.requests.show', $serviceRequest) }}">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="resident-muted">You have not submitted any requests yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="resident-card" style="margin-top: 24px;">
        <h2 style="font-size: 17px; margin: 0 0 10px;">My account</h2>
        <p class="resident-muted" style="margin: 0;"><strong>Name:</strong> {{ $user->name }}<br><strong>Email:</strong> {{ $user->email }}</p>
    </div>
@endsection
