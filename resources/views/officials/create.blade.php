@extends('layouts.app')

@section('title', 'Add Official | Barangay Information System')
@section('main-style', 'max-width: 750px;')

@section('breadcrumb')
    <a href="{{ route('officials.index') }}" style="color: inherit; text-decoration: none;">Barangay Officials</a>
    <i data-lucide="chevron-right"></i>
    <strong>Add Official</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; color: var(--ink);">Add New Official</h1>
        <p style="color: var(--muted-soft); font-size: 13px;">Register an elective or appointed official to the barangay council.</p>
    </div>
    <div class="record-form-page-card">
        <x-officials.form />
    </div>
@endsection
