@extends('layouts.app')

@section('title')
    Household {{ $household->household_number }} | Barangay Information System
@endsection

@section('breadcrumb')
    <a href="{{ route('households.index') }}" style="color: inherit; text-decoration: none;">Households</a>
    <i data-lucide="chevron-right"></i>
    <strong>{{ $household->household_number }}</strong>
@endsection

@section('content')
    <x-workspace.page-header :title="'Household: '.$household->household_number" description="Household details and registered family members." icon="house">
        <x-slot:actions>
            <a href="{{ route('households.edit', [$household]) }}" class="button button-primary"><i data-lucide="pencil" aria-hidden="true"></i> Edit household</a>
        </x-slot:actions>
    </x-workspace.page-header>

    <!-- Info Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 20px;">
            <span style="font-size: 11px; color: var(--muted-soft); text-transform: uppercase; font-weight: 600;">Household Head</span>
            <h2 style="font-size: 18px; color: var(--ink); margin-top: 6px;">{{ $household->household_head }}</h2>
        </div>

        <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 20px;">
            <span style="font-size: 11px; color: var(--muted-soft); text-transform: uppercase; font-weight: 600;">Address</span>
            <h2 style="font-size: 18px; color: var(--ink); margin-top: 6px;">{{ $household->address }}</h2>
        </div>

        <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 20px;">
            <span style="font-size: 11px; color: var(--muted-soft); text-transform: uppercase; font-weight: 600;">Total Members</span>
            <h2 style="font-size: 18px; color: var(--accent); margin-top: 6px;">{{ $household->residents->count() }}</h2>
        </div>
    </div>

    <!-- Family Members / Residents Table -->
    <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; overflow-x: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <div style="padding: 18px 20px; border-bottom: 1px solid var(--line); background: var(--canvas);">
            <h3 style="font-size: 15px; color: var(--ink); margin: 0;">Residents</h3>
        </div>

        <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: var(--canvas); border-bottom: 1px solid var(--line); text-align: left; color: var(--muted);">
                    <th style="padding: 12px 16px;">Name</th>
                    <th style="padding: 12px 16px;">Gender</th>
                    <th style="padding: 12px 16px;">Civil Status</th>
                    <th style="padding: 12px 16px;">Birthdate</th>
                    <th style="padding: 12px 16px;">Contact Number</th>
                    <th style="padding: 12px 16px;">Voter Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($household->residents as $resident)
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 12px 16px; font-weight: 600; color: var(--ink);">
                            {{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }}
                        </td>
                        <td style="padding: 12px 16px;">{{ $resident->gender }}</td>
                        <td style="padding: 12px 16px;">{{ $resident->civil_status }}</td>
                        <td style="padding: 12px 16px; color: var(--muted);">
                            {{ $resident->birthdate ? $resident->birthdate->format('M d, Y') : '-' }}
                        </td>
                        <td style="padding: 12px 16px; color: var(--muted);">{{ $resident->contact_number ?? 'N/A' }}</td>
                        <td style="padding: 12px 16px;">
                            @if ($resident->is_voter)
                                <span style="background: var(--success-soft); color: var(--success); padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Registered Voter</span>
                            @else
                                <span style="background: var(--surface-soft); color: var(--muted-soft); padding: 3px 8px; border-radius: 4px; font-size: 11px;">Non-Voter</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: var(--muted-soft);">
                            No residents registered in this household.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
