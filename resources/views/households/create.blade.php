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
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; color: var(--ink);">Add New Household</h1>
        <p style="color: var(--muted-soft); font-size: 13px;">Register a new household. Its number will be assigned automatically when you save.</p>
    </div>

    <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <x-households.create-form />
    </div>
@endsection
