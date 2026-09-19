@extends('layouts.app')

@section('title')Edit Official | Barangay Information System@endsection

@section('main-style', 'max-width: 750px;')

@section('breadcrumb')
    <a href="{{ route('officials.index') }}" style="color: inherit; text-decoration: none;">Barangay Officials</a>
    <i data-lucide="chevron-right"></i>
    <strong>Edit {{ $official->name }}</strong>
@endsection

@section('content')
    <div style="margin-bottom: 24px;">
        <a href="{{ route('officials.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #42634e; text-decoration: none; margin-bottom: 12px;">
            <i data-lucide="arrow-left"></i> Back to Officials
        </a>
        <h1 style="font-size: 24px; color: #1e3a29;">Edit Official: {{ $official->name }}</h1>
        <p style="color: #69786b; font-size: 13px;">Update official title, contact information, or term of service.</p>
    </div>

    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <form method="POST" action="{{ route('officials.update', $official) }}">
            @csrf
            @method('PUT')

            <!-- Full Name -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                    Full Name <span style="color: red;">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $official->name) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                @error('name') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <!-- Position & Contact -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                <div>
                    <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                        Position / Title <span style="color: red;">*</span>
                    </label>
                    <select name="position" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; background: white; box-sizing: border-box;">
                        <option value="">-- Select Position --</option>
                        <option value="Barangay Captain" {{ old('position', $official->position) == 'Barangay Captain' ? 'selected' : '' }}>Barangay Captain (Punong Barangay)</option>
                        <option value="Barangay Kagawad" {{ old('position', $official->position) == 'Barangay Kagawad' ? 'selected' : '' }}>Barangay Kagawad (Councilor)</option>
                        <option value="SK Chairman" {{ old('position', $official->position) == 'SK Chairman' ? 'selected' : '' }}>SK Chairman</option>
                        <option value="Barangay Secretary" {{ old('position', $official->position) == 'Barangay Secretary' ? 'selected' : '' }}>Barangay Secretary</option>
                        <option value="Barangay Treasurer" {{ old('position', $official->position) == 'Barangay Treasurer' ? 'selected' : '' }}>Barangay Treasurer</option>
                    </select>
                    @error('position') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                        Contact Number
                    </label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $official->contact_number) }}" placeholder="e.g. 09171234567" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                    @error('contact_number') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Term of Office -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                        Term Start Date
                    </label>
                    <input type="date" name="term_start" value="{{ old('term_start', $official->term_start ? $official->term_start->format('Y-m-d') : '') }}" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                    @error('term_start') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                        Term End Date
                    </label>
                    <input type="date" name="term_end" value="{{ old('term_end', $official->term_end ? $official->term_end->format('Y-m-d') : '') }}" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                    @error('term_end') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('officials.index') }}" class="button" style="text-decoration: none; padding: 10px 18px; border: 1px solid #ccd5c8; border-radius: 6px; color: #555;">
                    Cancel
                </a>
                <button type="submit" class="button button-primary" style="padding: 10px 22px; cursor: pointer;">
                    Update Official
                </button>
            </div>
        </form>
    </div>
@endsection
