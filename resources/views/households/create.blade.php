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
        <a href="{{ route('households.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #42634e; text-decoration: none; margin-bottom: 12px;">
            <i data-lucide="arrow-left"></i> Back to Households
        </a>
        <h1 style="font-size: 24px; color: #1e3a29;">Add New Household</h1>
        <p style="color: #69786b; font-size: 13px;">Register a new household in the barangay database.</p>
    </div>

    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <form method="POST" action="{{ route('households.store') }}">
            @csrf

            <!-- Household Number -->
            <div style="margin-bottom: 18px;">
                <x-form.label for="household_number" required>Household Number</x-form.label>
                <x-form.input type="text" name="household_number" value="{{ old('household_number') }}" required placeholder="e.g. HH-2026-001" />
                <x-form.error :message="$errors->first('household_number')" />
            </div>

            <!-- Household Head -->
            <div style="margin-bottom: 18px;">
                <x-form.label for="household_head" required>Household Head</x-form.label>
                <x-form.input type="text" name="household_head" value="{{ old('household_head') }}" required placeholder="Full name of household head" />
                <x-form.error :message="$errors->first('household_head')" />
            </div>

            <!-- Address -->
            <div style="margin-bottom: 24px;">
                <x-form.label for="address" required>Address / Purok</x-form.label>
                <x-form.input type="text" name="address" value="{{ old('address') }}" required placeholder="e.g. 124 Rizal St., Purok 3" />
                <x-form.error :message="$errors->first('address')" />
            </div>

            <!-- Submit Buttons -->
            <x-form.actions :cancel-url="route('households.index')">
                <x-slot:submit>Save Household</x-slot:submit>
            </x-form.actions>
        </form>
    </div>
@endsection
