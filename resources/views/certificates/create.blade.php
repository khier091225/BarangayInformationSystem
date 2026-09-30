@extends('layouts.app')

@section('title', 'Issue Certificate | Barangay Information System')
@section('main-style', 'max-width: 750px;')

@section('breadcrumb')
    <a href="{{ route('certificates.index') }}" style="color: inherit; text-decoration: none;">Certificates</a>
    <i data-lucide="chevron-right"></i>
    <strong>Issue Certificate</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; color: var(--ink);">Issue certificate</h1>
        <p style="color: var(--muted-soft); font-size: 13px;">Generate an official document for a registered barangay resident.</p>
    </div>
    <div class="record-form-page-card">
        <x-certificates.create-form :residents="$residents" :selected-resident-id="$selectedResidentId" />
    </div>
@endsection
