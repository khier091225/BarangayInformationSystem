@extends('layouts.app')

@section('title', 'Edit Official | Barangay Information System')
@section('main-style', 'max-width: 750px;')

@section('breadcrumb')
    <a href="{{ route('officials.index') }}" style="color: inherit; text-decoration: none;">Barangay Officials</a>
    <i data-lucide="chevron-right"></i>
    <strong>Edit {{ $official->name }}</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; color: var(--ink);">Edit Official: {{ $official->name }}</h1>
        <p style="color: var(--muted-soft); font-size: 13px;">Update official title, contact information, or term of service.</p>
    </div>
    <div class="record-form-page-card">
        <x-officials.form :official="$official" />
    </div>
@endsection
