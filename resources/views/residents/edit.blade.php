@extends('layouts.app')

@section('title', 'Edit Resident | Barangay Information System')
@section('main-style', 'max-width: 840px;')

@section('breadcrumb')
    <a href="{{ route('residents.index') }}" style="color: inherit; text-decoration: none;">Residents</a>
    <i data-lucide="chevron-right"></i>
    <strong>Edit {{ $resident->full_name }}</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; color: var(--ink);">Edit Resident: {{ $resident->full_name }}</h1>
        <p style="color: var(--muted-soft); font-size: 13px;">Update personal details, address, or voter status.</p>
    </div>
    <div class="record-form-page-card">
        <x-residents.form :resident="$resident" :households="$households" />
    </div>
@endsection
