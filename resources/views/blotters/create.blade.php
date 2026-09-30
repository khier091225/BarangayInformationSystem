@extends('layouts.app')

@section('title', 'Record Blotter | Barangay Information System')
@section('main-style', 'max-width: 800px;')

@section('breadcrumb')
    <a href="{{ route('blotters.index') }}" style="color: inherit; text-decoration: none;">Blotter Records</a>
    <i data-lucide="chevron-right"></i>
    <strong>Record blotter</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; color: var(--ink);">Record blotter</h1>
        <p style="color: var(--muted); font-size: 13px;">Record the complainant and what happened. New cases start as Pending.</p>
    </div>
    <div class="staff-request-detail-card">
        <x-blotters.create-form />
    </div>
@endsection
