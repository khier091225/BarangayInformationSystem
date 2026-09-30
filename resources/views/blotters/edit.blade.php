@extends('layouts.app')

@section('title', 'Edit Blotter #'.$blotter->id.' | Barangay Information System')
@section('main-style', 'max-width: 800px;')

@section('breadcrumb')
    <a href="{{ route('blotters.index') }}" style="color: inherit; text-decoration: none;">Blotter Records</a>
    <i data-lucide="chevron-right"></i>
    <strong>Edit blotter #{{ $blotter->id }}</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; color: var(--ink);">Edit blotter #{{ $blotter->id }}</h1>
        <p style="color: var(--muted-soft); font-size: 13px;">Update hearing status or incident details below.</p>
    </div>
    <div class="record-form-page-card">
        <x-blotters.edit-form :blotter="$blotter" />
    </div>
@endsection
