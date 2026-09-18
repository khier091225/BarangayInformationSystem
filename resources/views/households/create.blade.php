<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Add Household | Barangay Information System</title>
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
                <a href="{{ route('households.index') }}" class="selected"><i data-lucide="house"></i> Households</a>
                <a href="{{ route('certificates.index') }}"><i data-lucide="files"></i> Certificates</a>
                <a href="{{ route('blotters.index') }}"><i data-lucide="notebook-pen"></i> Blotter records</a>
                <a href="{{ route('officials.index') }}"><i data-lucide="badge-check"></i> Officials</a>
            </nav>
        </aside>

        <div class="workspace-shell">
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb">
                    <a href="{{ route('households.index') }}" style="color: inherit; text-decoration: none;">Households</a>
                    <i data-lucide="chevron-right"></i>
                    <strong>Add New Household</strong>
                </div>
            </header>

            <main class="workspace-main" style="padding: 24px 34px; max-width: 700px;">
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
                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                Household Number <span style="color: red;">*</span>
                            </label>
                            <input type="text" name="household_number" value="{{ old('household_number') }}" required placeholder="e.g. HH-2026-001" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                            @error('household_number') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <!-- Household Head -->
                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                Household Head <span style="color: red;">*</span>
                            </label>
                            <input type="text" name="household_head" value="{{ old('household_head') }}" required placeholder="Full name of household head" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                            @error('household_head') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <!-- Address -->
                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #2d3b30;">
                                Address / Purok <span style="color: red;">*</span>
                            </label>
                            <input type="text" name="address" value="{{ old('address') }}" required placeholder="e.g. 124 Rizal St., Purok 3" style="width: 100%; padding: 10px 12px; border: 1px solid #ccd5c8; border-radius: 6px; font-size: 13px; box-sizing: border-box;">
                            @error('address') <span style="color: #c0392b; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div style="display: flex; justify-content: flex-end; gap: 12px;">
                            <a href="{{ route('households.index') }}" class="button" style="text-decoration: none; padding: 10px 18px; border: 1px solid #ccd5c8; border-radius: 6px; color: #555;">
                                Cancel
                            </a>
                            <button type="submit" class="button button-primary" style="padding: 10px 22px; cursor: pointer;">
                                Save Household
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </body>
</html>