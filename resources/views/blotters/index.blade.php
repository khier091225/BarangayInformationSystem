@extends('layouts.app')

@section('title')
    Blotter Records | Barangay Information System
@endsection

@section('breadcrumb')
    <span>Workspace</span>
    <i data-lucide="chevron-right"></i>
    <strong>Blotter Records</strong>
@endsection

@section('content')
    <div class="workspace-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 26px; color: var(--ink); margin-top: 4px;">Blotter records</h1>
            <p style="color: var(--muted-soft); font-size: 13px;">Manage community disputes, complaints, and hearing records.</p>
        </div>
        <a href="{{ route('blotters.create') }}" class="button button-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;" data-record-dialog-trigger aria-haspopup="dialog" aria-controls="blotter-create-dialog">
            <i data-lucide="plus"></i> Record blotter
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="search-filter-card">
        <form method="GET" action="{{ route('blotters.index') }}" class="search-filter-form">
            <div class="search-input-group">
                <i data-lucide="search" class="search-icon" aria-hidden="true"></i>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search complainant, respondent, or incident..." aria-label="Search blotter records">
            </div>
            
                    <select name="status" class="search-select" aria-label="Filter by case status">
                        <option value="">All Statuses</option>
                        @foreach (\App\Models\Blotter::STATUSES as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status === \App\Models\Blotter::STATUS_MEDIATION ? 'Under Mediation' : $status }}</option>
                        @endforeach
            </select>

            <button type="submit" class="search-button-primary">
                <i data-lucide="search" aria-hidden="true"></i>
                <span>Filter</span>
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('blotters.index') }}" class="search-button-reset">
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
                    <th style="padding: 14px 16px;">Case #</th>
                    <th style="padding: 14px 16px;">Complainant</th>
                    <th style="padding: 14px 16px;">Respondent</th>
                    <th style="padding: 14px 16px;">Incident Details</th>
                    <th style="padding: 14px 16px;">Date</th>
                    <th style="padding: 14px 16px;">Status</th>
                    <th style="padding: 14px 16px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($blotters as $blotter)
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 14px 16px; font-weight: 600; color: var(--ink);">#{{ $blotter->id }}</td>
                        <td style="padding: 14px 16px; font-weight: 600;">{{ $blotter->complainant }}</td>
                        <td style="padding: 14px 16px; color: var(--danger-hover);">{{ $blotter->respondent }}</td>
                        <td style="padding: 14px 16px; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $blotter->incident }}">
                            {{ $blotter->incident }}
                        </td>
                        <td style="padding: 14px 16px; color: var(--muted);">
                            {{ $blotter->incident_date ? $blotter->incident_date->format('M d, Y') : '-' }}
                        </td>
                        <td style="padding: 14px 16px;">
                            <x-blotter-status :status="$blotter->status" />
                        </td>
                        <td style="padding: 14px 16px; text-align: right;">
                            <div style="display: inline-flex; gap: 8px;">
                                <a href="{{ route('blotters.show', [$blotter]) }}" style="color: var(--accent); text-decoration: none; font-size: 12px; font-weight: 600; padding: 4px 8px; border: 1px solid var(--line-strong); border-radius: 4px;">
                                    View
                                </a>
                                <a href="{{ route('blotters.edit', [$blotter]) }}" style="color: var(--accent); text-decoration: none; font-size: 12px; font-weight: 600; padding: 4px 8px; border: 1px solid var(--line-strong); border-radius: 4px;" data-record-dialog-trigger aria-haspopup="dialog" aria-controls="blotter-edit-dialog-{{ $blotter->getKey() }}">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('blotters.destroy', [$blotter]) }}" onsubmit="return confirm('Are you sure you want to delete this blotter record?');" style="display: inline;">
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
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--muted-soft);">
                            No blotter records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $blotters->links() }}
    </div>

    <x-record-dialog id="blotter-create-dialog" title="Record blotter" description="Record the complainant and what happened. New cases start as Pending." icon="notebook-pen" :wide="true" :open-on-load="$errors->any() && old('_record_form') === 'blotters.create'">
        <x-blotters.create-form :modal="true" />
    </x-record-dialog>

    @foreach ($blotters as $blotter)
        <x-record-dialog :id="'blotter-edit-dialog-'.$blotter->getKey()" title="Edit blotter record" :description="'Update case #'.$blotter->getKey().' and its hearing status.'" icon="notebook-pen" :wide="true" :open-on-load="$errors->any() && old('_record_form') === 'blotters.edit.'.$blotter->getKey()">
            <x-blotters.edit-form :blotter="$blotter" :modal="true" />
        </x-record-dialog>
    @endforeach
@endsection
