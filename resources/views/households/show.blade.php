<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Household {{ $household->household_number }} | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="workspace-page">
        <!-- Sidebar Navigation -->
        <aside class="workspace-sidebar">
            <a href="{{ route('home') }}" class="brand workspace-brand">
                <span class="brand-mark"><i data-lucide="landmark"></i></span>
                <span class="brand-name">Barangay<span>INFORMATION SYSTEM</span></span>
            </a>
            <div class="workspace-label">STAFF WORKSPACE</div>
            <nav class="workspace-nav">
                <a href="{{ route('dashboard') }}"><i data-lucide="layout-dashboard"></i> Overview</a>
                <span class="nav-group-label">BARANGAY MANAGEMENT</span>
                <a href="{{ route('dashboard') }}#residents"><i data-lucide="users-round"></i> Residents</a>
                <a href="{{ route('households.index') }}" class="selected"><i data-lucide="house"></i> Households</a>
                <a href="{{ route('dashboard') }}#certificates"><i data-lucide="files"></i> Certificates</a>
                <a href="{{ route('blotters.index') }}"><i data-lucide="notebook-pen"></i> Blotter records</a>
                <a href="{{ route('dashboard') }}#officials"><i data-lucide="badge-check"></i> Officials</a>
            </nav>
        </aside>

        <div class="workspace-shell">
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb">
                    <a href="{{ route('households.index') }}" style="color: inherit; text-decoration: none;">Households</a>
                    <i data-lucide="chevron-right"></i>
                    <strong>{{ $household->household_number }}</strong>
                </div>
            </header>

            <main class="workspace-main" style="padding: 24px 34px;">
                <div style="margin-bottom: 24px;">
                    <a href="{{ route('households.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #42634e; text-decoration: none; margin-bottom: 12px;">
                        <i data-lucide="arrow-left"></i> Back to Households
                    </a>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h1 style="font-size: 24px; color: #1e3a29;">Household: {{ $household->household_number }}</h1>
                            <p style="color: #69786b; font-size: 13px;">Household details and registered family members.</p>
                        </div>
                        <a href="{{ route('households.edit', $household) }}" class="button button-outline" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                            <i data-lucide="pencil"></i> Edit Household
                        </a>
                    </div>
                </div>

                <!-- Info Cards -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
                    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 20px;">
                        <span style="font-size: 11px; color: #788577; text-transform: uppercase; font-weight: 600;">Household Head</span>
                        <h2 style="font-size: 18px; color: #1e3a29; margin-top: 6px;">{{ $household->household_head }}</h2>
                    </div>

                    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 20px;">
                        <span style="font-size: 11px; color: #788577; text-transform: uppercase; font-weight: 600;">Address / Purok</span>
                        <h2 style="font-size: 18px; color: #1e3a29; margin-top: 6px;">{{ $household->address }}</h2>
                    </div>

                    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 20px;">
                        <span style="font-size: 11px; color: #788577; text-transform: uppercase; font-weight: 600;">Total Members</span>
                        <h2 style="font-size: 18px; color: #276747; margin-top: 6px;">{{ $household->residents->count() }} Residents</h2>
                    </div>
                </div>

                <!-- Family Members / Residents Table -->
                <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div style="padding: 18px 20px; border-bottom: 1px solid #edf1eb; background: #fbfdfa;">
                        <h3 style="font-size: 15px; color: #1e3a29; margin: 0;">Family Members (Residents)</h3>
                    </div>

                    <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8faf7; border-bottom: 1px solid #e3e8e1; text-align: left; color: #5a6b5c;">
                                <th style="padding: 12px 16px;">Name</th>
                                <th style="padding: 12px 16px;">Gender</th>
                                <th style="padding: 12px 16px;">Civil Status</th>
                                <th style="padding: 12px 16px;">Birthdate</th>
                                <th style="padding: 12px 16px;">Contact Number</th>
                                <th style="padding: 12px 16px;">Voter Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($household->residents as $resident)
                                <tr style="border-bottom: 1px solid #edf1eb;">
                                    <td style="padding: 12px 16px; font-weight: 600; color: #1e3a29;">
                                        {{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }}
                                    </td>
                                    <td style="padding: 12px 16px;">{{ $resident->gender }}</td>
                                    <td style="padding: 12px 16px;">{{ $resident->civil_status }}</td>
                                    <td style="padding: 12px 16px; color: #556658;">
                                        {{ $resident->birthdate ? $resident->birthdate->format('M d, Y') : '-' }}
                                    </td>
                                    <td style="padding: 12px 16px; color: #556658;">{{ $resident->contact_number ?? 'N/A' }}</td>
                                    <td style="padding: 12px 16px;">
                                        @if ($resident->is_voter)
                                            <span style="background: #eaf5eb; color: #236539; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Registered Voter</span>
                                        @else
                                            <span style="background: #f4f4f4; color: #777; padding: 3px 8px; border-radius: 4px; font-size: 11px;">Non-Voter</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px; color: #829283;">
                                        No residents registered in this household.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </body>
</html>