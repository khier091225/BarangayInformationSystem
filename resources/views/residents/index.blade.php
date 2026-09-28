@extends('layouts.app')

@section('title')
    Residents | Barangay Information System
@endsection

@section('breadcrumb')
    <span>Workspace</span>
    <i data-lucide="chevron-right"></i>
    <strong>Residents</strong>
@endsection

@section('content')
    <div class="workspace-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <div class="eyebrow">BARANGAY DIRECTORY</div>
            <h1 style="font-size: 26px; color: var(--ink); margin-top: 4px;">Resident Registry</h1>
            <p style="color: var(--muted-soft); font-size: 13px;">Manage community residents, demographics, and voter registration records.</p>
        </div>
        <a href="{{ route('residents.create') }}" class="button button-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="user-plus"></i> Register Resident
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="search-filter-card">
        <form method="GET" action="{{ route('residents.index') }}" class="search-filter-form">
            <div class="search-input-group">
                <i data-lucide="search" class="search-icon" aria-hidden="true"></i>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by name, address, or contact..." aria-label="Search residents">
            </div>
            
            <select name="gender" class="search-select" aria-label="Filter by gender">
                <option value="">All Genders</option>
                <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
            </select>

            <select name="is_voter" class="search-select" aria-label="Filter by voter status">
                <option value="">All Voters</option>
                <option value="1" {{ request('is_voter') === '1' ? 'selected' : '' }}>Registered Voters</option>
                <option value="0" {{ request('is_voter') === '0' ? 'selected' : '' }}>Non-Voters</option>
            </select>

            <button type="submit" class="search-button-primary">
                <i data-lucide="search" aria-hidden="true"></i>
                <span>Filter</span>
            </button>
            @if(request('search') || request('gender') || request('is_voter') !== null)
                <a href="{{ route('residents.index') }}" class="search-button-reset">
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
                    <th style="padding: 14px 16px;">Full Name</th>
                    <th style="padding: 14px 16px;">Household</th>
                    <th style="padding: 14px 16px;">Gender & Age</th>
                    <th style="padding: 14px 16px;">Civil Status</th>
                    <th style="padding: 14px 16px;">Address</th>
                    <th style="padding: 14px 16px;">Voter</th>
                    <th style="padding: 14px 16px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($residents as $resident)
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 14px 16px; font-weight: 600; color: var(--ink);">
                            <a href="{{ route('residents.show', [$resident]) }}" style="color: inherit; text-decoration: underline;">
                                {{ $resident->full_name }}
                            </a>
                            @if($resident->contact_number)
                                <div style="font-size: 11px; color: var(--muted-soft); font-weight: 400; margin-top: 2px;">
                                    <i data-lucide="phone" style="width: 11px; height: 11px; display: inline;"></i> {{ $resident->contact_number }}
                                </div>
                            @endif
                        </td>
                        <td style="padding: 14px 16px;">
                            @if($resident->household)
                                <a href="{{ route('households.show', [$resident->household]) }}" style="color: var(--accent); text-decoration: none; font-weight: 600;">
                                    {{ $resident->household->household_number }}
                                </a>
                                <div style="font-size: 11px; color: var(--muted-soft);">Head: {{ $resident->household->household_head }}</div>
                            @else
                                <span style="color: var(--muted-soft); font-style: italic;">No Household</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px;">
                            <span>{{ $resident->gender }}</span>,
                            <span style="color: var(--muted);">
                                {{ $resident->birthdate ? $resident->birthdate->age . ' yrs old' : 'N/A' }}
                            </span>
                        </td>
                        <td style="padding: 14px 16px; color: var(--muted);">
                            {{ $resident->civil_status }}
                        </td>
                        <td style="padding: 14px 16px; color: var(--muted); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $resident->address }}">
                            {{ $resident->address }}
                        </td>
                        <td style="padding: 14px 16px;">
                            @if ($resident->is_voter)
                                <span style="background: var(--success-soft); color: var(--success); padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Voter</span>
                            @else
                                <span style="background: var(--surface-soft); color: var(--muted); padding: 3px 8px; border-radius: 4px; font-size: 11px;">Non-Voter</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; text-align: right;">
                            <div style="display: inline-flex; gap: 8px;">
                                <a href="{{ route('residents.show', [$resident]) }}" style="color: var(--accent); text-decoration: none; font-size: 12px; font-weight: 600; padding: 4px 8px; border: 1px solid var(--line-strong); border-radius: 4px;">
                                    View
                                </a>
                                <a href="{{ route('residents.edit', [$resident]) }}" style="color: var(--muted); text-decoration: none; font-size: 12px; font-weight: 600; padding: 4px 8px; border: 1px solid var(--input-line); border-radius: 4px;">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('residents.destroy', [$resident]) }}" onsubmit="return confirm('Are you sure you want to delete this resident record?');" style="display: inline;">
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
                            No resident records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $residents->links() }}
    </div>
@endsection
