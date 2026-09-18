<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Register Resident | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="workspace-page">
        <!-- Sidebar Navigation -->
        <aside class="workspace-sidebar">
            <a href="{{ route('dashboard') }}" class="brand workspace-brand">
                <span class="brand-mark"><i data-lucide="landmark"></i></span>
                <span class="brand-name">Barangay<span>INFORMATION SYSTEM</span></span>
            </a>
            <div class="workspace-label">STAFF WORKSPACE</div>
            <nav class="workspace-nav">
                <a href="{{ route('dashboard') }}"><i data-lucide="layout-dashboard"></i> Overview</a>
                <span class="nav-group-label">BARANGAY MANAGEMENT</span>
                <a href="{{ route('residents.index') }}" class="selected"><i data-lucide="users-round"></i> Residents</a>
                <a href="{{ route('households.index') }}"><i data-lucide="house"></i> Households</a>
                <a href="{{ route('certificates.index') }}"><i data-lucide="files"></i> Certificates</a>
                <a href="{{ route('blotters.index') }}"><i data-lucide="notebook-pen"></i> Blotter records</a>
                <a href="{{ route('officials.index') }}"><i data-lucide="badge-check"></i> Officials</a>
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-profile">
                    <span class="staff-avatar" aria-hidden="true">BS</span>
                    <span><strong>Barangay Staff</strong><small>Authorized Portal</small></span>
                </div>
            </div>
        </aside>

        <div class="workspace-shell">
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb">
                    <a href="{{ route('residents.index') }}" style="color: inherit; text-decoration: none;">Residents</a>
                    <i data-lucide="chevron-right"></i>
                    <strong>Register New Resident</strong>
                </div>
            </header>

            <main class="workspace-main" style="padding: 24px 34px; max-width: 840px;">
                <div style="margin-bottom: 24px;">
                    <a href="{{ route('residents.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #42634e; text-decoration: none; margin-bottom: 12px;">
                        <i data-lucide="arrow-left"></i> Back to Residents
                    </a>
                    <h1 style="font-size: 24px; color: #1e3a29;">Register New Resident</h1>
                    <p style="color: #69786b; font-size: 13px;">Add a community resident to the barangay registry.</p>
                </div>

                <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <form method="POST" action="{{ route('residents.store') }}">
                        @csrf

                        <!-- Full Name Row (3 columns) -->
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 18px;">
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    First Name <span style="color: red;">*</span>
                                </label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="e.g. Juan" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                                @error('first_name') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Middle Name
                                </label>
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}" placeholder="e.g. Santos" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                                @error('middle_name') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Last Name <span style="color: red;">*</span>
                                </label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="e.g. Dela Cruz" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                                @error('last_name') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Household Association & Birthdate -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Household (Optional)
                                </label>
                                <select name="household_id" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; background: white; box-sizing: border-box;">
                                    <option value="">-- No Household / Independent --</option>
                                    @foreach ($households as $household)
                                        <option value="{{ $household->id }}" {{ old('household_id') == $household->id ? 'selected' : '' }}>
                                            {{ $household->household_number }} (Head: {{ $household->household_head }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('household_id') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Birthdate <span style="color: red;">*</span>
                                </label>
                                <input type="date" name="birthdate" value="{{ old('birthdate') }}" required max="{{ date('Y-m-d') }}" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                                @error('birthdate') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Gender & Civil Status -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Gender <span style="color: red;">*</span>
                                </label>
                                <select name="gender" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; background: white; box-sizing: border-box;">
                                    <option value="">-- Select Gender --</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Civil Status <span style="color: red;">*</span>
                                </label>
                                <select name="civil_status" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; background: white; box-sizing: border-box;">
                                    <option value="">-- Select Civil Status --</option>
                                    <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                                    <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                </select>
                                @error('civil_status') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Address & Contact Number -->
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Address / Purok <span style="color: red;">*</span>
                                </label>
                                <input type="text" name="address" value="{{ old('address') }}" required placeholder="e.g. Purok 4, Ilang-Ilang St." style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                                @error('address') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Contact Number
                                </label>
                                <input type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder="e.g. 09171234567" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                                @error('contact_number') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Voter Status Checkbox -->
                        <div style="margin-bottom: 26px; padding: 14px; background: #fbfdfa; border: 1px solid #edf1eb; border-radius: 6px;">
                            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; font-weight: 600; color: #2d3b30;">
                                <input type="checkbox" name="is_voter" value="1" {{ old('is_voter') ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: #276747;">
                                <span>Registered Voter in this Barangay</span>
                            </label>
                            <p style="margin: 4px 0 0 26px; font-size: 12px; color: #788577;">Check this box if the resident is registered to vote in local elections.</p>
                        </div>

                        <!-- Submit Buttons -->
                        <div style="display: flex; justify-content: flex-end; gap: 12px;">
                            <a href="{{ route('residents.index') }}" class="button" style="text-decoration: none; padding: 10px 18px; border: 1px solid #ccd5c8; border-radius: 6px; color: #555;">
                                Cancel
                            </a>
                            <button type="submit" class="button button-primary" style="padding: 10px 22px; cursor: pointer;">
                                Save Resident
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </body>
</html>

