@extends('layouts.app')

@section('title', 'Register Resident | Barangay Information System')
@section('main-style', 'max-width: 840px;')

@section('breadcrumb')
    <a href="{{ route('residents.index') }}" style="color: inherit; text-decoration: none;">Residents</a>
    <i data-lucide="chevron-right"></i>
    <strong>Register New Resident</strong>
@endsection

@section('content')
    <x-workspace.page-header title="Register new resident" description="Add a community resident to the barangay registry." icon="user-plus" />
    <div class="record-form-page-card">
        <x-residents.form :households="$households" />
    </div>
@endsection
