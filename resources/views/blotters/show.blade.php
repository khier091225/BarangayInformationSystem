@extends('layouts.app')

@section('title')
    Blotter #{{ $blotter->id }} | Barangay Information System
@endsection

@section('main-style', 'max-width: 800px;')

@section('breadcrumb')
    <a href="{{ route('blotters.index') }}" style="color: inherit; text-decoration: none;">Blotter Records</a>
    <i data-lucide="chevron-right"></i>
    <strong>Case #{{ $blotter->id }}</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <a href="{{ route('blotters.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--muted); text-decoration: none; margin-bottom: 12px;">
            <i data-lucide="arrow-left"></i> Back to Blotters
        </a>
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px;">
            <h1 style="font-size: 24px; color: var(--ink);">Blotter Report #{{ $blotter->id }}</h1>
            <a href="{{ route('blotters.edit', [$blotter]) }}" class="button button-outline" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i data-lucide="pencil"></i> Edit Blotter
            </a>
        </div>
    </div>

    <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <dl style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 0 0 24px;">
            <div>
                <dt style="font-size: 13px; color: var(--muted-soft);">Complainant</dt>
                <dd style="margin: 6px 0 0; font-weight: 600; color: var(--ink); overflow-wrap: anywhere;">{{ $blotter->complainant }}</dd>
            </div>
            <div>
                <dt style="font-size: 13px; color: var(--muted-soft);">Respondent</dt>
                <dd style="margin: 6px 0 0; font-weight: 600; color: var(--ink); overflow-wrap: anywhere;">{{ $blotter->respondent }}</dd>
            </div>
            <div>
                <dt style="font-size: 13px; color: var(--muted-soft);">Incident Date</dt>
                <dd style="margin: 6px 0 0; color: var(--ink);">{{ $blotter->incident_date?->format('M d, Y') ?? 'Not recorded' }}</dd>
            </div>
            <div>
                <dt style="font-size: 13px; color: var(--muted-soft);">Status</dt>
                <dd style="margin: 6px 0 0; font-weight: 600; color: var(--ink);">{{ $blotter->status }}</dd>
            </div>
        </dl>
        <h2 style="font-size: 16px; color: var(--ink);">Incident Details</h2>
        <p style="white-space: pre-wrap; overflow-wrap: anywhere; line-height: 1.6; color: var(--muted);">{{ $blotter->incident }}</p>
    </div>
@endsection
