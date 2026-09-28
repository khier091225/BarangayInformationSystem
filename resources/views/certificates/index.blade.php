@extends('layouts.app')

@section('title')
    Certificates & Clearances | Barangay Information System
@endsection

@section('breadcrumb')
    <span>Workspace</span>
    <i data-lucide="chevron-right"></i>
    <strong>Certificates & Clearances</strong>
@endsection

@section('content')
    <div class="workspace-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <div class="eyebrow">DOCUMENT ISSUANCE</div>
            <h1 style="font-size: 26px; color: var(--ink); margin-top: 4px;">Certificates & Clearances</h1>
            <p style="color: var(--muted-soft); font-size: 13px;">Manage and print official barangay clearances, residency, and indigency certificates.</p>
        </div>
        <a href="{{ route('certificates.create') }}" class="button button-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="file-plus"></i> Issue Certificate
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="search-filter-card">
        <form method="GET" action="{{ route('certificates.index') }}" class="search-filter-form">
            <div class="search-input-group">
                <i data-lucide="search" class="search-icon" aria-hidden="true"></i>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by resident name, certificate type, or purpose..." aria-label="Search certificates">
            </div>
            
            <select name="type" class="search-select" aria-label="Filter by certificate type">
                <option value="">All Document Types</option>
                <option value="Barangay Clearance" {{ request('type') == 'Barangay Clearance' ? 'selected' : '' }}>Barangay Clearance</option>
                <option value="Certificate of Residency" {{ request('type') == 'Certificate of Residency' ? 'selected' : '' }}>Certificate of Residency</option>
                <option value="Certificate of Indigency" {{ request('type') == 'Certificate of Indigency' ? 'selected' : '' }}>Certificate of Indigency</option>
                <option value="Business Clearance" {{ request('type') == 'Business Clearance' ? 'selected' : '' }}>Business Clearance</option>
            </select>

            <button type="submit" class="search-button-primary">
                <i data-lucide="search" aria-hidden="true"></i>
                <span>Filter</span>
            </button>
            @if(request('search') || request('type'))
                <a href="{{ route('certificates.index') }}" class="search-button-reset">
                    <i data-lucide="rotate-ccw" aria-hidden="true"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table -->
    <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: var(--canvas); border-bottom: 1px solid var(--line); text-align: left; color: var(--muted);">
                    <th style="padding: 14px 16px;">Control #</th>
                    <th style="padding: 14px 16px;">Resident Name</th>
                    <th style="padding: 14px 16px;">Document Type</th>
                    <th style="padding: 14px 16px;">Purpose</th>
                    <th style="padding: 14px 16px;">Date Issued</th>
                    <th style="padding: 14px 16px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($certificates as $cert)
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 14px 16px; font-weight: 700; color: var(--ink);">
                            #CERT-{{ str_pad($cert->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td style="padding: 14px 16px; font-weight: 600;">
                            @if($cert->resident)
                                <a href="{{ route('residents.show', [$cert->resident]) }}" style="color: inherit; text-decoration: underline;">
                                    {{ $cert->resident->full_name }}
                                </a>
                            @else
                                <span style="color: var(--muted-soft);">Resident record unavailable</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600;
                                @if($cert->certificate_type == 'Barangay Clearance') background: var(--success-soft); color: var(--success);
                                @elseif($cert->certificate_type == 'Certificate of Residency') background: var(--info-soft); color: var(--info);
                                @elseif($cert->certificate_type == 'Certificate of Indigency') background: var(--warning-soft); color: var(--warning);
                                @else background: var(--canvas); color: var(--muted); @endif">
                                {{ $cert->certificate_type }}
                            </span>
                        </td>
                        <td style="padding: 14px 16px; color: var(--muted);">
                            {{ $cert->purpose }}
                        </td>
                        <td style="padding: 14px 16px; color: var(--muted);">
                            {{ $cert->date_issued ? $cert->date_issued->format('M d, Y') : '-' }}
                        </td>
                        <td style="padding: 14px 16px; text-align: right;">
                            <div style="display: inline-flex; gap: 8px;">
                                <a href="{{ route('certificates.show', [$cert]) }}" style="color: var(--accent); text-decoration: none; font-size: 12px; font-weight: 600; padding: 4px 10px; border: 1px solid var(--line-strong); border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i data-lucide="printer" style="width: 12px; height: 12px;"></i> View & Print
                                </a>
                                <form method="POST" action="{{ route('certificates.destroy', [$cert]) }}" onsubmit="return confirm('Are you sure you want to delete this certificate record?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="record-delete">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--muted-soft);">
                            No certificates or clearances issued yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $certificates->links() }}
    </div>
@endsection
