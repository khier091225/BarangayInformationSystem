@extends('layouts.app')

@section('title', 'Record Blotter | Barangay Information System')
@section('main-style', 'max-width: 800px;')

@section('breadcrumb')
    <a href="{{ route('blotters.index') }}" style="color: inherit; text-decoration: none;">Blotter Records</a>
    <i data-lucide="chevron-right"></i>
    <strong>Record blotter</strong>
@endsection

@section('content')
    <x-workspace.page-header title="Record blotter" description="Record the complainant and what happened. New cases start as Pending." icon="notebook-pen" />
    <div class="staff-request-detail-card">
        <x-blotters.create-form />
    </div>
@endsection
