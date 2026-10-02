@extends('layouts.app')

@section('title', 'Add Official | Barangay Information System')
@section('main-style', 'max-width: 750px;')

@section('breadcrumb')
    <a href="{{ route('officials.index') }}" style="color: inherit; text-decoration: none;">Barangay Officials</a>
    <i data-lucide="chevron-right"></i>
    <strong>Add Official</strong>
@endsection

@section('content')
    <x-workspace.page-header title="Add New Official" description="Register an elective or appointed official to the barangay council." icon="user-plus" />
    <div class="record-form-page-card">
        <x-officials.form />
    </div>
@endsection
