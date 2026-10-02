@extends('layouts.app')

@section('title', 'Edit Official | Barangay Information System')
@section('main-style', 'max-width: 750px;')

@section('breadcrumb')
    <a href="{{ route('officials.index') }}" style="color: inherit; text-decoration: none;">Barangay Officials</a>
    <i data-lucide="chevron-right"></i>
    <strong>Edit {{ $official->name }}</strong>
@endsection

@section('content')
    <x-workspace.page-header :title="'Edit official: '.$official->name" description="Update the official title, contact information, or term of service." icon="pencil" />
    <div class="record-form-page-card">
        <x-officials.form :official="$official" />
    </div>
@endsection
