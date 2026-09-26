@extends('layouts.app')

@section('title')
    Add Official | Barangay Information System
@endsection

@section('main-style', 'max-width: 750px;')

@section('breadcrumb')
    <a href="{{ route('officials.index', ['role' => 'admin']) }}" style="color: inherit; text-decoration: none;">Barangay Officials</a>
    <i data-lucide="chevron-right"></i>
    <strong>Add Official</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <a href="{{ route('officials.index', ['role' => 'admin']) }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #42634e; text-decoration: none; margin-bottom: 12px;">
            <i data-lucide="arrow-left"></i> Back to Officials
        </a>
        <h1 style="font-size: 24px; color: #1e3a29;">Add New Official</h1>
        <p style="color: #69786b; font-size: 13px;">Register an elective or appointed official to the barangay council.</p>
    </div>

    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <form method="POST" action="{{ route('officials.store', ['role' => 'admin']) }}">
            @csrf

            <!-- Full Name -->
            <div style="margin-bottom: 18px;">
                <x-form.label for="name" required>Full Name</x-form.label>
                <x-form.input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Hon. Juan Dela Cruz" />
                <x-form.error :message="$errors->first('name')" />
            </div>

            <!-- Position & Contact -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                <div>
                    <x-form.label for="position" required>Position / Title</x-form.label>
                    <x-form.select name="position" required>
                        <option value="">-- Select Position --</option>
                        <option value="Barangay Captain" {{ old('position') == 'Barangay Captain' ? 'selected' : '' }}>Barangay Captain (Punong Barangay)</option>
                        <option value="Barangay Kagawad" {{ old('position') == 'Barangay Kagawad' ? 'selected' : '' }}>Barangay Kagawad (Councilor)</option>
                        <option value="SK Chairman" {{ old('position') == 'SK Chairman' ? 'selected' : '' }}>SK Chairman</option>
                        <option value="Barangay Secretary" {{ old('position') == 'Barangay Secretary' ? 'selected' : '' }}>Barangay Secretary</option>
                        <option value="Barangay Treasurer" {{ old('position') == 'Barangay Treasurer' ? 'selected' : '' }}>Barangay Treasurer</option>
                    </x-form.select>
                    <x-form.error :message="$errors->first('position')" />
                </div>

                <div>
                    <x-form.label for="contact_number">Contact Number</x-form.label>
                    <x-form.input type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder="e.g. 09171234567" />
                    <x-form.error :message="$errors->first('contact_number')" />
                </div>
            </div>

            <!-- Term of Office -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <x-form.label for="term_start">Term Start Date</x-form.label>
                    <x-form.input type="date" name="term_start" value="{{ old('term_start') }}" />
                    <x-form.error :message="$errors->first('term_start')" />
                </div>

                <div>
                    <x-form.label for="term_end">Term End Date</x-form.label>
                    <x-form.input type="date" name="term_end" value="{{ old('term_end') }}" />
                    <x-form.error :message="$errors->first('term_end')" />
                </div>
            </div>

            <!-- Submit Buttons -->
            <x-form.actions :cancel-url="route('officials.index', ['role' => 'admin'])">
                <x-slot:submit>Save Official</x-slot:submit>
            </x-form.actions>
        </form>
    </div>
@endsection
