@extends('layouts.app')

@section('title')
    Households | Barangay Information System
@endsection

@section('breadcrumb')
    <span>Workspace</span>
    <i data-lucide="chevron-right"></i>
    <strong>Households</strong>
@endsection

@section('content')
    <div class="workspace-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <div class="eyebrow">BARANGAY DIRECTORY</div>
            <h1 style="font-size: 26px; color: var(--ink); margin-top: 4px;">Household Registry</h1>
            <p style="color: var(--muted-soft); font-size: 13px;">Manage registered households, household heads, and family addresses.</p>
        </div>
        <a href="{{ route('households.create') }}" class="button button-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus"></i> Add Household
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="search-filter-card">
        <form method="GET" action="{{ route('households.index') }}" class="search-filter-form">
            <div class="search-input-group">
                <i data-lucide="search" class="search-icon" aria-hidden="true"></i>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search household number, head, or address..." aria-label="Search households">
            </div>
            <button type="submit" class="search-button-primary">
                <i data-lucide="search" aria-hidden="true"></i>
                <span>Search</span>
            </button>
            @if(request('search'))
                <a href="{{ route('households.index') }}" class="search-button-reset">
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
                    <th style="padding: 14px 16px;">Household #</th>
                    <th style="padding: 14px 16px;">Household Head</th>
                    <th style="padding: 14px 16px;">Address</th>
                    <th style="padding: 14px 16px; text-align: center;">Family Members</th>
                    <th style="padding: 14px 16px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($households as $household)
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 14px 16px; font-weight: 700; color: var(--ink);">
                            <a href="{{ route('households.show', [$household]) }}" style="color: inherit; text-decoration: underline;">
                                {{ $household->household_number }}
                            </a>
                        </td>
                        <td style="padding: 14px 16px; font-weight: 600;">{{ $household->household_head }}</td>
                        <td style="padding: 14px 16px; color: var(--muted);">{{ $household->address }}</td>
                        <td style="padding: 14px 16px; text-align: center;">
                            <span style="background: var(--accent-soft); color: var(--accent); font-weight: 600; font-size: 12px; padding: 4px 10px; border-radius: 20px;">
                                {{ $household->residents_count }} members
                            </span>
                        </td>
                        <td style="padding: 14px 16px; text-align: right;">
                            <div style="display: inline-flex; gap: 8px;">
                                <a href="{{ route('households.show', [$household]) }}" style="color: var(--accent); text-decoration: none; font-size: 12px; font-weight: 600; padding: 4px 8px; border: 1px solid var(--line-strong); border-radius: 4px;">
                                    View Members
                                </a>
                                <a href="{{ route('households.edit', [$household]) }}" style="color: var(--muted); text-decoration: none; font-size: 12px; font-weight: 600; padding: 4px 8px; border: 1px solid var(--input-line); border-radius: 4px;">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('households.destroy', [$household]) }}" onsubmit="return confirm('Are you sure you want to delete this household?');" style="display: inline;">
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
                            No households found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $households->links() }}
    </div>
@endsection
