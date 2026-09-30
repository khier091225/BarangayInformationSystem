@extends('layouts.app')

@section('title', 'Register Resident | Barangay Information System')
@section('main-style', 'max-width: 840px;')

@section('breadcrumb')
    <a href="{{ route('residents.index') }}" style="color: inherit; text-decoration: none;">Residents</a>
    <i data-lucide="chevron-right"></i>
    <strong>Register New Resident</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; color: var(--ink);">Register New Resident</h1>
        <p style="color: var(--muted-soft); font-size: 13px;">Add a community resident to the barangay registry.</p>
    </div>
    <div class="record-form-page-card">
        <x-residents.form :households="$households" />
    </div>
@endsection
