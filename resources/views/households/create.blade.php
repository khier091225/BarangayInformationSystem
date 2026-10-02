@extends('layouts.app')

@section('title')
    Add Household | Barangay Information System
@endsection

@section('main-style', 'max-width: 700px;')

@section('breadcrumb')
    <a href="{{ route('households.index') }}" style="color: inherit; text-decoration: none;">Households</a>
    <i data-lucide="chevron-right"></i>
    <strong>Add New Household</strong>
@endsection

@section('content')
    <x-workspace.page-header title="Add new household" description="Register a new household. Its number will be assigned automatically when you save." icon="house" />

    <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <x-households.create-form />
    </div>
@endsection
