@extends('layouts.app')

@section('title')
    Edit Household {{ $household->household_number }} | Barangay Information System
@endsection

@section('main-style', 'max-width: 700px;')

@section('breadcrumb')
    <a href="{{ route('households.index') }}" style="color: inherit; text-decoration: none;">Households</a>
    <i data-lucide="chevron-right"></i>
    <strong>Edit {{ $household->household_number }}</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; color: var(--ink);">Edit Household</h1>
        <p style="color: var(--muted-soft); font-size: 13px;">Update household information below.</p>
    </div>

    <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <form method="POST" action="{{ route('households.update', [$household]) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 18px;">
                <span style="display: block; margin-bottom: 6px; color: var(--muted); font-size: 13px; font-weight: 600;">Household Number</span>
                <strong style="display: block; color: var(--ink); font-size: 15px;">{{ $household->household_number }}</strong>
                <small style="display: block; margin-top: 4px; color: var(--muted-soft);">Assigned by the system and cannot be changed.</small>
            </div>

            <!-- Household Head -->
            <div style="margin-bottom: 18px;">
                <x-form.label for="household_head" required>Household Head</x-form.label>
                <x-form.input type="text" name="household_head" value="{{ old('household_head', $household->household_head) }}" required />
                <x-form.error :message="$errors->first('household_head')" />
            </div>

            <!-- Address -->
            <div style="margin-bottom: 24px;">
                <x-form.label for="address" required>Address</x-form.label>
                <x-form.input type="text" name="address" value="{{ old('address', $household->address) }}" required />
                <x-form.error :message="$errors->first('address')" />
            </div>

            <!-- Submit Buttons -->
            <x-form.actions :cancel-url="route('households.index')">
                <x-slot:submit>Update Household</x-slot:submit>
            </x-form.actions>
        </form>
    </div>
@endsection
