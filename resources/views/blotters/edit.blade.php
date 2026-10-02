@extends('layouts.app')

@section('title', 'Edit Blotter #'.$blotter->id.' | Barangay Information System')
@section('main-style', 'max-width: 800px;')

@section('breadcrumb')
    <a href="{{ route('blotters.index') }}" style="color: inherit; text-decoration: none;">Blotter Records</a>
    <i data-lucide="chevron-right"></i>
    <strong>Edit blotter #{{ $blotter->id }}</strong>
@endsection

@section('content')
    <x-workspace.page-header :title="'Edit blotter #'.$blotter->id" description="Correct the names, incident date, or case information." icon="pencil" />
    <div class="record-form-page-card">
        <x-blotters.edit-form :blotter="$blotter" />
    </div>
@endsection
