<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Issue Certificate | Barangay Information System</title>
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
                <a href="{{ route('residents.index') }}"><i data-lucide="users-round"></i> Residents</a>
                <a href="{{ route('households.index') }}"><i data-lucide="house"></i> Households</a>
                <a href="{{ route('certificates.index') }}" class="selected"><i data-lucide="files"></i> Certificates</a>
                <a href="{{ route('blotters.index') }}"><i data-lucide="notebook-pen"></i> Blotter records</a>
                <a href="{{ route('officials.index') }}"><i data-lucide="badge-check"></i> Officials</a>
            </nav>
        </aside>

        <div class="workspace-shell">
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb">
                    <a href="{{ route('certificates.index') }}" style="color: inherit; text-decoration: none;">Certificates & Clearances</a>
                    <i data-lucide="chevron-right"></i>
                    <strong>Issue Certificate</strong>
                </div>
            </header>

            <main class="workspace-main" style="padding: 24px 34px; max-width: 750px;">
                <div style="margin-bottom: 24px;">
                    <a href="{{ route('certificates.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #42634e; text-decoration: none; margin-bottom: 12px;">
                        <i data-lucide="arrow-left"></i> Back to Certificates
                    </a>
                    <h1 style="font-size: 24px; color: #1e3a29;">Issue New Certificate / Clearance</h1>
                    <p style="color: #69786b; font-size: 13px;">Generate an official document for a registered barangay resident.</p>
                </div>

                <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <form method="POST" action="{{ route('certificates.store') }}">
                        @csrf

                        <!-- Select Resident -->
                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                Select Resident <span style="color: red;">*</span>
                            </label>
                            <select name="resident_id" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; background: white; box-sizing: border-box;">
                                <option value="">-- Choose Resident --</option>
                                @foreach ($residents as $resident)
                                    <option value="{{ $resident->id }}" {{ (old('resident_id', $selectedResidentId) == $resident->id) ? 'selected' : '' }}>
                                        {{ $resident->last_name }}, {{ $resident->first_name }} {{ $resident->middle_name }} ({{ $resident->address }})
                                    </option>
                                @endforeach
                            </select>
                            @error('resident_id') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <!-- Certificate Type & Date Issued -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Certificate Type <span style="color: red;">*</span>
                                </label>
                                <select name="certificate_type" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; background: white; box-sizing: border-box;">
                                    <option value="">-- Select Document Type --</option>
                                    <option value="Barangay Clearance" {{ old('certificate_type') == 'Barangay Clearance' ? 'selected' : '' }}>Barangay Clearance</option>
                                    <option value="Certificate of Residency" {{ old('certificate_type') == 'Certificate of Residency' ? 'selected' : '' }}>Certificate of Residency</option>
                                    <option value="Certificate of Indigency" {{ old('certificate_type') == 'Certificate of Indigency' ? 'selected' : '' }}>Certificate of Indigency</option>
                                    <option value="Business Clearance" {{ old('certificate_type') == 'Business Clearance' ? 'selected' : '' }}>Business Clearance</option>
                                </select>
                                @error('certificate_type') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Date Issued <span style="color: red;">*</span>
                                </label>
                                <input type="date" name="date_issued" value="{{ old('date_issued', date('Y-m-d')) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                                @error('date_issued') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Purpose -->
                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                Purpose / Reason <span style="color: red;">*</span>
                            </label>
                            <input type="text" name="purpose" value="{{ old('purpose') }}" required placeholder="e.g. Local Employment, Scholarship Application, Bank Account Requirement" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                            @error('purpose') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div style="display: flex; justify-content: flex-end; gap: 12px;">
                            <a href="{{ route('certificates.index') }}" class="button" style="text-decoration: none; padding: 10px 18px; border: 1px solid #ccd5c8; border-radius: 6px; color: #555;">
                                Cancel
                            </a>
                            <button type="submit" class="button button-primary" style="padding: 10px 22px; cursor: pointer;">
                                Issue & Preview Certificate
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </body>
</html>