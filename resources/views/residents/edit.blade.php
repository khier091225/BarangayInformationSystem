@extends('layouts.app')

@section('title')
    Edit Resident | Barangay Information System
@endsection

@section('main-style', 'max-width: 840px;')

@section('breadcrumb')
    <a href="{{ route('residents.index') }}" style="color: inherit; text-decoration: none;">Residents</a>
    <i data-lucide="chevron-right"></i>
    <strong>Edit {{ $resident->full_name }}</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <a href="{{ route('residents.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #42634e; text-decoration: none; margin-bottom: 12px;">
            <i data-lucide="arrow-left"></i> Back to Residents
        </a>
        <h1 style="font-size: 24px; color: #1e3a29;">Edit Resident: {{ $resident->full_name }}</h1>
        <p style="color: #69786b; font-size: 13px;">Update personal details, address, or voter status.</p>
    </div>

    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <form method="POST" action="{{ route('residents.update', $resident) }}">
            @csrf
            @method('PUT')

            <!-- Full Name Row (3 columns) -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 18px;">
                <div>
                    <x-form.label for="first_name" required>First Name</x-form.label>
                    <x-form.input type="text" name="first_name" value="{{ old('first_name', $resident->first_name) }}" required />
                    <x-form.error :message="$errors->first('first_name')" />
                </div>

                <div>
                    <x-form.label for="middle_name">Middle Name</x-form.label>
                    <x-form.input type="text" name="middle_name" value="{{ old('middle_name', $resident->middle_name) }}" />
                    <x-form.error :message="$errors->first('middle_name')" />
                </div>

                <div>
                    <x-form.label for="last_name" required>Last Name</x-form.label>
                    <x-form.input type="text" name="last_name" value="{{ old('last_name', $resident->last_name) }}" required />
                    <x-form.error :message="$errors->first('last_name')" />
                </div>
            </div>

            <!-- Household Association & Birthdate -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                <div>
                    <x-form.label for="household_id">Household (Optional)</x-form.label>
                    <x-form.select name="household_id">
                        <option value="">-- No Household / Independent --</option>
                        @foreach ($households as $household)
                            <option value="{{ $household->id }}" {{ old('household_id', $resident->household_id) == $household->id ? 'selected' : '' }}>
                                {{ $household->household_number }} (Head: {{ $household->household_head }})
                            </option>
                        @endforeach
                    </x-form.select>
                    <x-form.error :message="$errors->first('household_id')" />
                </div>

                <div>
                    <x-form.label for="birthdate" required>Birthdate</x-form.label>
                    <x-form.input type="date" name="birthdate" value="{{ old('birthdate', $resident->birthdate ? $resident->birthdate->format('Y-m-d') : '') }}" required max="{{ date('Y-m-d') }}" />
                    <x-form.error :message="$errors->first('birthdate')" />
                </div>
            </div>

            <!-- Gender & Civil Status -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                <div>
                    <x-form.label for="gender" required>Gender</x-form.label>
                    <x-form.select name="gender" required>
                        <option value="Male" {{ old('gender', $resident->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $resident->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                    </x-form.select>
                    <x-form.error :message="$errors->first('gender')" />
                </div>

                <div>
                    <x-form.label for="civil_status" required>Civil Status</x-form.label>
                    <x-form.select name="civil_status" required>
                        <option value="Single" {{ old('civil_status', $resident->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Married" {{ old('civil_status', $resident->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                        <option value="Widowed" {{ old('civil_status', $resident->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="Separated" {{ old('civil_status', $resident->civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                        <option value="Divorced" {{ old('civil_status', $resident->civil_status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                    </x-form.select>
                    <x-form.error :message="$errors->first('civil_status')" />
                </div>
            </div>

            <!-- Address & Contact Number -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <x-form.label for="address" required>Address / Purok</x-form.label>
                    <x-form.input type="text" name="address" value="{{ old('address', $resident->address) }}" required />
                    <x-form.error :message="$errors->first('address')" />
                </div>

                <div>
                    <x-form.label for="contact_number">Contact Number</x-form.label>
                    <x-form.input type="text" name="contact_number" value="{{ old('contact_number', $resident->contact_number) }}" />
                    <x-form.error :message="$errors->first('contact_number')" />
                </div>
            </div>

            <!-- Voter Status Checkbox -->
            <div style="margin-bottom: 26px; padding: 14px; background: #fbfdfa; border: 1px solid #edf1eb; border-radius: 6px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; font-weight: 600; color: #2d3b30;">
                    <input type="checkbox" name="is_voter" value="1" {{ old('is_voter', $resident->is_voter) ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: #276747;">
                    <span>Registered Voter in this Barangay</span>
                </label>
                <p style="margin: 4px 0 0 26px; font-size: 12px; color: #788577;">Check this box if the resident is registered to vote in local elections.</p>
            </div>

            <!-- Submit Buttons -->
            <x-form.actions :cancel-url="route('residents.index')">
                <x-slot:submit>Update Resident</x-slot:submit>
            </x-form.actions>
        </form>
    </div>
@endsection
