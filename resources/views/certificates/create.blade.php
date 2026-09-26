@extends('layouts.app')

@section('title')
    Issue Certificate | Barangay Information System
@endsection

@section('main-style', 'max-width: 750px;')

@section('breadcrumb')
    <a href="{{ route('certificates.index', ['role' => 'admin']) }}" style="color: inherit; text-decoration: none;">Certificates & Clearances</a>
    <i data-lucide="chevron-right"></i>
    <strong>Issue Certificate</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <a href="{{ route('certificates.index', ['role' => 'admin']) }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #42634e; text-decoration: none; margin-bottom: 12px;">
            <i data-lucide="arrow-left"></i> Back to Certificates
        </a>
        <h1 style="font-size: 24px; color: #1e3a29;">Issue New Certificate / Clearance</h1>
        <p style="color: #69786b; font-size: 13px;">Generate an official document for a registered barangay resident.</p>
    </div>

    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <form method="POST" action="{{ route('certificates.store', ['role' => 'admin']) }}">
            @csrf

            <!-- Select Resident -->
            <div style="margin-bottom: 18px;">
                <x-form.label for="resident_id" required>Select Resident</x-form.label>
                <x-form.select name="resident_id" required>
                    <option value="">-- Choose Resident --</option>
                    @foreach ($residents as $resident)
                        <option value="{{ $resident->id }}" {{ (old('resident_id', $selectedResidentId) == $resident->id) ? 'selected' : '' }}>
                            {{ $resident->last_name }}, {{ $resident->first_name }} {{ $resident->middle_name }} ({{ $resident->address }})
                        </option>
                    @endforeach
                </x-form.select>
                <x-form.error :message="$errors->first('resident_id')" />
            </div>

            <!-- Certificate Type & Date Issued -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                <div>
                    <x-form.label for="certificate_type" required>Certificate Type</x-form.label>
                    <x-form.select name="certificate_type" required>
                        <option value="">-- Select Document Type --</option>
                        <option value="Barangay Clearance" {{ old('certificate_type') == 'Barangay Clearance' ? 'selected' : '' }}>Barangay Clearance</option>
                        <option value="Certificate of Residency" {{ old('certificate_type') == 'Certificate of Residency' ? 'selected' : '' }}>Certificate of Residency</option>
                        <option value="Certificate of Indigency" {{ old('certificate_type') == 'Certificate of Indigency' ? 'selected' : '' }}>Certificate of Indigency</option>
                        <option value="Business Clearance" {{ old('certificate_type') == 'Business Clearance' ? 'selected' : '' }}>Business Clearance</option>
                    </x-form.select>
                    <x-form.error :message="$errors->first('certificate_type')" />
                </div>

                <div>
                    <x-form.label for="date_issued" required>Date Issued</x-form.label>
                    <x-form.input type="date" name="date_issued" value="{{ old('date_issued', date('Y-m-d')) }}" required />
                    <x-form.error :message="$errors->first('date_issued')" />
                </div>
            </div>

            <!-- Purpose -->
            <div style="margin-bottom: 24px;">
                <x-form.label for="purpose" required>Purpose / Reason</x-form.label>
                <x-form.input type="text" name="purpose" value="{{ old('purpose') }}" required placeholder="e.g. Local Employment, Scholarship Application, Bank Account Requirement" />
                <x-form.error :message="$errors->first('purpose')" />
            </div>

            <!-- Submit Buttons -->
            <x-form.actions :cancel-url="route('certificates.index', ['role' => 'admin'])">
                <x-slot:submit>Issue & Preview Certificate</x-slot:submit>
            </x-form.actions>
        </form>
    </div>
@endsection
