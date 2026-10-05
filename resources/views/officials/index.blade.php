@extends('layouts.app')

@section('title')
    Barangay Officials | Barangay Information System
@endsection

@section('breadcrumb')
    <span>Workspace</span>
    <i data-lucide="chevron-right"></i>
    <strong>Barangay Officials</strong>
@endsection

@section('content')
    <x-workspace.page-header title="Barangay Officials" description="Manage elective and appointed community leaders, roles, and service terms." icon="badge-check">
        <x-slot:actions>
            <a href="{{ route('officials.create') }}" class="button button-primary" data-record-dialog-trigger aria-haspopup="dialog" aria-controls="official-create-dialog"><i data-lucide="user-plus" aria-hidden="true"></i> Add official</a>
        </x-slot:actions>
    </x-workspace.page-header>

    <!-- Search & Filters -->
    <div class="search-filter-card">
        <form method="GET" action="{{ route('officials.index') }}" class="search-filter-form">
            <div class="search-input-group">
                <i data-lucide="search" class="search-icon" aria-hidden="true"></i>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by name, position, or contact..." aria-label="Search officials">
            </div>
            
            <select name="position" class="search-select" aria-label="Filter by official position">
                <option value="">All Positions</option>
                <option value="Barangay Captain" {{ request('position') == 'Barangay Captain' ? 'selected' : '' }}>Barangay Captain</option>
                <option value="Barangay Kagawad" {{ request('position') == 'Barangay Kagawad' ? 'selected' : '' }}>Barangay Kagawad</option>
                <option value="SK Chairman" {{ request('position') == 'SK Chairman' ? 'selected' : '' }}>SK Chairman</option>
                <option value="Barangay Secretary" {{ request('position') == 'Barangay Secretary' ? 'selected' : '' }}>Barangay Secretary</option>
                <option value="Barangay Treasurer" {{ request('position') == 'Barangay Treasurer' ? 'selected' : '' }}>Barangay Treasurer</option>
            </select>

            <button type="submit" class="search-button-primary">
                <i data-lucide="search" aria-hidden="true"></i>
                <span>Filter</span>
            </button>
            @if(request('search') || request('position'))
                <a href="{{ route('officials.index') }}" class="search-button-reset">
                    <i data-lucide="rotate-ccw" aria-hidden="true"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table -->
    <x-workspace.table-scroll label="Barangay officials">
        <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: var(--canvas); border-bottom: 1px solid var(--line); text-align: left; color: var(--muted);">
                    <th style="padding: 14px 16px;">Official Name</th>
                    <th style="padding: 14px 16px;">Position</th>
                    <th style="padding: 14px 16px;">Contact Number</th>
                    <th style="padding: 14px 16px;">Term of Office</th>
                    <th style="padding: 14px 16px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($officials as $official)
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 14px 16px; font-weight: 600; color: var(--ink);">
                            {{ $official->name }}
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600;
                                @if($official->position == 'Barangay Captain') background: var(--success-soft); color: var(--success);
                                @elseif($official->position == 'SK Chairman') background: var(--info-soft); color: var(--info);
                                @elseif($official->position == 'Barangay Kagawad') background: var(--warning-soft); color: var(--warning);
                                @else background: var(--canvas); color: var(--muted); @endif">
                                {{ $official->position }}
                            </span>
                        </td>
                        <td style="padding: 14px 16px; color: var(--muted);">
                            {{ $official->contact_number ?: 'Not provided' }}
                        </td>
                        <td style="padding: 14px 16px; color: var(--muted);">
                            @if($official->term_start && $official->term_end)
                                {{ $official->term_start->format('M Y') }} – {{ $official->term_end->format('M Y') }}
                            @elseif($official->term_start)
                                Since {{ $official->term_start->format('M Y') }}
                            @else
                                <span style="color: var(--muted-soft);">Active</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; text-align: right;">
                            <div style="display: inline-flex; gap: 8px;">
                                <a href="{{ route('officials.edit', [$official]) }}" style="color: var(--muted); text-decoration: none; font-size: 12px; font-weight: 600; padding: 4px 8px; border: 1px solid var(--input-line); border-radius: 4px;" data-record-dialog-trigger aria-haspopup="dialog" aria-controls="official-edit-dialog-{{ $official->getKey() }}">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('officials.destroy', [$official]) }}" onsubmit="return confirm('Are you sure you want to remove this official?');" data-confirm-title="Delete official?" data-confirm-message="Delete the official record for {{ $official->name }}?" data-confirm-label="Delete official" style="display: inline;">
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
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--muted-soft);">
                            No barangay officials registered yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-workspace.table-scroll>

    <div style="margin-top: 20px;">
        {{ $officials->links() }}
    </div>

    <x-record-dialog id="official-create-dialog" title="Add official" icon="badge-check" :open-on-load="$errors->any() && old('_record_form') === 'officials.create'">
        <x-officials.form :modal="true" />
    </x-record-dialog>

    @foreach ($officials as $official)
        <x-record-dialog :id="'official-edit-dialog-'.$official->getKey()" title="Edit official" :description="'Update the details for '.$official->name.'.'" icon="badge-check" :open-on-load="$errors->any() && old('_record_form') === 'officials.edit.'.$official->getKey()">
            <x-officials.form :official="$official" :modal="true" />
        </x-record-dialog>
    @endforeach
@endsection
