@extends('layouts.app')

@section('title', 'Edit Resident | Barangay Information System')
@section('main-style', 'max-width: 840px;')

@section('breadcrumb')
    <a href="{{ route('residents.index') }}" style="color: inherit; text-decoration: none;">Residents</a>
    <i data-lucide="chevron-right"></i>
    <strong>Edit {{ $resident->full_name }}</strong>
@endsection

@section('content')
    <x-workspace.page-header :title="'Edit resident: '.$resident->full_name" description="Update personal details, address, or voter status." icon="pencil" />
    <div class="record-form-page-card">
        <x-residents.form :resident="$resident" :households="$households" />
    </div>
@endsection
