@extends('layouts.app')

@section('title', 'Issue Certificate | Barangay Information System')
@section('main-style', 'max-width: 750px;')

@section('breadcrumb')
    <a href="{{ route('certificates.index') }}" style="color: inherit; text-decoration: none;">Certificates</a>
    <i data-lucide="chevron-right"></i>
    <strong>Issue Certificate</strong>
@endsection

@section('content')
    <x-workspace.page-header title="Issue certificate" description="Generate an official document for a registered barangay resident." icon="file-plus" />
    <div class="record-form-page-card">
        <x-certificates.create-form :residents="$residents" :selected-resident-id="$selectedResidentId" />
    </div>
@endsection
