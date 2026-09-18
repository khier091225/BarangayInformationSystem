<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Edit Blotter #{{ $blotter->id }} | Barangay Information System</title>
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
                <a href="{{ route('dashboard') }}#certificates"><i data-lucide="files"></i> Certificates</a>
                <a href="{{ route('blotters.index') }}" class="selected"><i data-lucide="notebook-pen"></i> Blotter records</a>
                <a href="{{ route('officials.index') }}"><i data-lucide="badge-check"></i> Officials</a>
            </nav>
        </aside>

        <div class="workspace-shell">
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb">
                    <a href="{{ route('blotters.index') }}" style="color: inherit; text-decoration: none;">Blotter Records</a>
                    <i data-lucide="chevron-right"></i>
                    <strong>Edit Case #{{ $blotter->id }}</strong>
                </div>
            </header>

            <main class="workspace-main" style="padding: 24px 34px; max-width: 800px;">
                <div style="margin-bottom: 24px;">
                    <a href="{{ route('blotters.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #42634e; text-decoration: none; margin-bottom: 12px;">
                        <i data-lucide="arrow-left"></i> Back to Blotters
                    </a>
                    <h1 style="font-size: 24px; color: #1e3a29;">Edit Blotter Report #{{ $blotter->id }}</h1>
                    <p style="color: #69786b; font-size: 13px;">Update hearing status or incident details below.</p>
                </div>

                <!-- Form Card -->
                <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <form method="POST" action="{{ route('blotters.update', $blotter) }}">
                        @csrf
                        @method('PUT')

                        <!-- Complainant & Respondent -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Complainant Name <span style="color: red;">*</span>
                                </label>
                                <input type="text" name="complainant" value="{{ old('complainant', $blotter->complainant) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                                @error('complainant') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Respondent Name <span style="color: red;">*</span>
                                </label>
                                <input type="text" name="respondent" value="{{ old('respondent', $blotter->respondent) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                                @error('respondent') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Date & Status -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Incident Date <span style="color: red;">*</span>
                                </label>
                                <input type="date" name="incident_date" value="{{ old('incident_date', $blotter->incident_date ? $blotter->incident_date->format('Y-m-d') : '') }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                                @error('incident_date') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                    Status <span style="color: red;">*</span>
                                </label>
                                <select name="status" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; background: white; box-sizing: border-box;">
                                    <option value="Pending" {{ old('status', $blotter->status) == 'Pending' ? 'selected' : '' }}>Pending (Ongoing hearing)</option>
                                    <option value="Settled" {{ old('status', $blotter->status) == 'Settled' ? 'selected' : '' }}>Settled (Resolved)</option>
                                    <option value="Dismissed" {{ old('status', $blotter->status) == 'Dismissed' ? 'selected' : '' }}>Dismissed (Dropped/Dismissed)</option>
                                </select>
                                @error('status') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Incident Narrative -->
                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                Incident Details <span style="color: red;">*</span>
                            </label>
                            <textarea name="incident" rows="5" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box; line-height: 1.6;">{{ old('incident', $blotter->incident) }}</textarea>
                            @error('incident') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div style="display: flex; justify-content: flex-end; gap: 12px;">
                            <a href="{{ route('blotters.index') }}" class="button" style="text-decoration: none; padding: 10px 18px; border: 1px solid #ccd5c8; border-radius: 6px; color: #555;">
                                Cancel
                            </a>
                            <button type="submit" class="button button-primary" style="padding: 10px 22px; cursor: pointer;">
                                Update Blotter Report
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </body>
</html>