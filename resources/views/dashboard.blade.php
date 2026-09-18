<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#11665e">
        <title>Dashboard | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="workspace-page">
        <!-- Sidebar Navigation -->
        <aside class="workspace-sidebar" id="workspace-navigation" aria-label="Workspace navigation">
            <a href="{{ route('dashboard') }}" class="brand workspace-brand">
                <span class="brand-mark"><i data-lucide="landmark"></i></span>
                <span class="brand-name">Barangay<span>INFORMATION SYSTEM</span></span>
            </a>
            <div class="workspace-label">STAFF WORKSPACE</div>
            <nav class="workspace-nav" aria-label="Main workspace">
                <a href="{{ route('dashboard') }}" class="selected" aria-current="page"><i data-lucide="layout-dashboard"></i> Overview</a>
                <span class="nav-group-label">BARANGAY MANAGEMENT</span>
                <a href="{{ route('residents.index') }}"><i data-lucide="users-round"></i> Residents</a>
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

        <!-- Main Content Area -->
        <div class="workspace-shell">
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb">
                    <button type="button" class="icon-button sidebar-toggle" aria-label="Open sidebar" aria-controls="workspace-navigation" aria-expanded="false">
                        <i data-lucide="panel-left" aria-hidden="true"></i>
                    </button>
                    <span>Workspace</span>
                    <i data-lucide="chevron-right"></i>
                    <strong>Overview</strong>
                </div>
            </header>

            <main class="workspace-main" id="workspace-main" tabindex="-1" style="padding: 24px 34px;">
                <!-- Header -->
                <div class="workspace-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <div class="eyebrow">BARANGAY ADMINISTRATION</div>
                        <h1 style="font-size: 26px; color: #1e3a29; margin-top: 4px;">Management Dashboard</h1>
                        <p style="color: #69786b; font-size: 13px;">Real-time overview of community demographics, civil documents, and blotter records.</p>
                    </div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <a href="{{ route('residents.create') }}" class="button button-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                            <i data-lucide="user-plus"></i> Register Resident
                        </a>
                        <a href="{{ route('certificates.create') }}" class="button button-outline" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                            <i data-lucide="file-plus"></i> Issue Certificate
                        </a>
                        <a href="{{ route('blotters.create') }}" class="button button-outline" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                            <i data-lucide="plus"></i> File Complaint
                        </a>
                    </div>
                </div>

                <!-- Metrics Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px;">
                    <!-- Total Residents -->
                    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; border-radius: 8px; background: #eaf5eb; color: #245838; display: grid; place-items: center; flex-shrink: 0;">
                            <i data-lucide="users-round" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 12px; font-weight: 600; color: #69786b; text-transform: uppercase; letter-spacing: 0.04em;">Total Residents</div>
                            <div style="font-size: 26px; font-weight: 700; color: #1e3a29; margin-top: 2px;">{{ number_format($residentCount) }}</div>
                            <a href="{{ route('residents.index') }}" style="font-size: 12px; color: #276747; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                                View directory <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Households -->
                    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; border-radius: 8px; background: #e8f0fa; color: #285881; display: grid; place-items: center; flex-shrink: 0;">
                            <i data-lucide="house" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 12px; font-weight: 600; color: #69786b; text-transform: uppercase; letter-spacing: 0.04em;">Households</div>
                            <div style="font-size: 26px; font-weight: 700; color: #1e3a29; margin-top: 2px;">{{ number_format($householdCount) }}</div>
                            <a href="{{ route('households.index') }}" style="font-size: 12px; color: #285881; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                                View households <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Certificates Issued -->
                    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; border-radius: 8px; background: #f3eef8; color: #5f377e; display: grid; place-items: center; flex-shrink: 0;">
                            <i data-lucide="files" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 12px; font-weight: 600; color: #69786b; text-transform: uppercase; letter-spacing: 0.04em;">Certificates</div>
                            <div style="font-size: 26px; font-weight: 700; color: #1e3a29; margin-top: 2px;">{{ number_format($certificateCount) }}</div>
                            <a href="{{ route('certificates.index') }}" style="font-size: 12px; color: #5f377e; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                                View requests <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Blotter Records -->
                    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; border-radius: 8px; background: #fff2d5; color: #74500b; display: grid; place-items: center; flex-shrink: 0;">
                            <i data-lucide="notebook-pen" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 12px; font-weight: 600; color: #69786b; text-transform: uppercase; letter-spacing: 0.04em;">Pending Blotters</div>
                            <div style="font-size: 26px; font-weight: 700; color: #1e3a29; margin-top: 2px;">{{ number_format($pendingBlotterCount) }}</div>
                            <a href="{{ route('blotters.index') }}" style="font-size: 12px; color: #74500b; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                                Total cases: {{ $totalBlotterCount }} <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Shortcuts Row -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 28px;">
                    <a href="{{ route('residents.index') }}" style="background: white; border: 1px solid #e1e7de; border-radius: 6px; padding: 14px 18px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: #1e3a29; font-weight: 600; font-size: 13px; transition: border-color 0.2s;">
                        <span style="display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="users-round" style="width: 16px; height: 16px; color: #276747;"></i> Resident Registry
                        </span>
                        <i data-lucide="chevron-right" style="width: 14px; height: 14px; color: #999;"></i>
                    </a>
                    <a href="{{ route('households.index') }}" style="background: white; border: 1px solid #e1e7de; border-radius: 6px; padding: 14px 18px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: #1e3a29; font-weight: 600; font-size: 13px; transition: border-color 0.2s;">
                        <span style="display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="house" style="width: 16px; height: 16px; color: #285881;"></i> Households
                        </span>
                        <i data-lucide="chevron-right" style="width: 14px; height: 14px; color: #999;"></i>
                    </a>
                    <a href="{{ route('certificates.index') }}" style="background: white; border: 1px solid #e1e7de; border-radius: 6px; padding: 14px 18px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: #1e3a29; font-weight: 600; font-size: 13px; transition: border-color 0.2s;">
                        <span style="display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="files" style="width: 16px; height: 16px; color: #5f377e;"></i> Certificates
                        </span>
                        <i data-lucide="chevron-right" style="width: 14px; height: 14px; color: #999;"></i>
                    </a>
                    <a href="{{ route('blotters.index') }}" style="background: white; border: 1px solid #e1e7de; border-radius: 6px; padding: 14px 18px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: #1e3a29; font-weight: 600; font-size: 13px; transition: border-color 0.2s;">
                        <span style="display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="notebook-pen" style="width: 16px; height: 16px; color: #8b3f20;"></i> Blotter Records
                        </span>
                        <i data-lucide="chevron-right" style="width: 14px; height: 14px; color: #999;"></i>
                    </a>
                    <a href="{{ route('officials.index') }}" style="background: white; border: 1px solid #e1e7de; border-radius: 6px; padding: 14px 18px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: #1e3a29; font-weight: 600; font-size: 13px; transition: border-color 0.2s;">
                        <span style="display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="badge-check" style="width: 16px; height: 16px; color: #2e7d32;"></i> Officials
                        </span>
                        <i data-lucide="chevron-right" style="width: 14px; height: 14px; color: #999;"></i>
                    </a>
                </div>

                <!-- Recent Tables Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 24px; margin-bottom: 30px;">
                    <!-- Recent Certificate Requests -->
                    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <div style="padding: 16px 20px; border-bottom: 1px solid #e3e8e1; display: flex; justify-content: space-between; align-items: center; background: #f8faf7;">
                            <div>
                                <h2 style="font-size: 16px; font-weight: 600; color: #1e3a29; margin: 0;">Recent Certificate Requests</h2>
                                <p style="font-size: 12px; color: #69786b; margin: 2px 0 0;">Latest applications submitted</p>
                            </div>
                            <a href="{{ route('certificates.index') }}" style="font-size: 12px; font-weight: 600; color: #276747; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                View all <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                            </a>
                        </div>
                        <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="border-bottom: 1px solid #edf1eb; text-align: left; color: #5a6b5c; font-size: 12px;">
                                    <th style="padding: 12px 20px;">Resident</th>
                                    <th style="padding: 12px 16px;">Certificate Type</th>
                                    <th style="padding: 12px 16px;">Date Issued</th>
                                    <th style="padding: 12px 20px; text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentCertificates as $certificate)
                                    <tr style="border-bottom: 1px solid #edf1eb;">
                                        <td style="padding: 12px 20px; font-weight: 600; color: #1e3a29;">
                                            {{ $certificate->resident?->full_name ?? 'Unknown Resident' }}
                                        </td>
                                        <td style="padding: 12px 16px; color: #4b584e;">
                                            <span style="background: #f0f4f1; color: #2d5037; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 500;">
                                                {{ $certificate->certificate_type }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px 16px; color: #69786b; font-size: 12px;">
                                            {{ $certificate->date_issued ? $certificate->date_issued->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td style="padding: 12px 20px; text-align: right;">
                                            <a href="{{ route('certificates.show', $certificate) }}" style="color: #276747; text-decoration: none; font-size: 12px; font-weight: 600; padding: 3px 8px; border: 1px solid #c8d8c9; border-radius: 4px;">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 28px; color: #829283;">
                                            No recent certificate requests.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Recent Blotter Records -->
                    <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <div style="padding: 16px 20px; border-bottom: 1px solid #e3e8e1; display: flex; justify-content: space-between; align-items: center; background: #f8faf7;">
                            <div>
                                <h2 style="font-size: 16px; font-weight: 600; color: #1e3a29; margin: 0;">Recent Blotter Cases</h2>
                                <p style="font-size: 12px; color: #69786b; margin: 2px 0 0;">Peace and order incidents</p>
                            </div>
                            <a href="{{ route('blotters.index') }}" style="font-size: 12px; font-weight: 600; color: #276747; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                View all <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                            </a>
                        </div>
                        <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="border-bottom: 1px solid #edf1eb; text-align: left; color: #5a6b5c; font-size: 12px;">
                                    <th style="padding: 12px 20px;">Complainant / Respondent</th>
                                    <th style="padding: 12px 16px;">Incident</th>
                                    <th style="padding: 12px 16px;">Status</th>
                                    <th style="padding: 12px 20px; text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentBlotters as $blotter)
                                    <tr style="border-bottom: 1px solid #edf1eb;">
                                        <td style="padding: 12px 20px; font-weight: 600; color: #1e3a29;">
                                            <div>{{ $blotter->complainant }}</div>
                                            <div style="font-size: 11px; color: #69786b; font-weight: 400;">vs. {{ $blotter->respondent }}</div>
                                        </td>
                                        <td style="padding: 12px 16px; color: #4b584e;">
                                            <div style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $blotter->incident }}">
                                                {{ $blotter->incident }}
                                            </div>
                                            <div style="font-size: 11px; color: #829283;">{{ $blotter->incident_date ? $blotter->incident_date->format('M d, Y') : '' }}</div>
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            @if ($blotter->status === 'Pending')
                                                <span style="background: #fff2d5; color: #74500b; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Pending</span>
                                            @elseif ($blotter->status === 'Settled')
                                                <span style="background: #eaf5eb; color: #236539; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Settled</span>
                                            @else
                                                <span style="background: #fbece5; color: #8b3f20; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">{{ $blotter->status }}</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 20px; text-align: right;">
                                            <a href="{{ route('blotters.show', $blotter) }}" style="color: #276747; text-decoration: none; font-size: 12px; font-weight: 600; padding: 3px 8px; border: 1px solid #c8d8c9; border-radius: 4px;">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 28px; color: #829283;">
                                            No blotter records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Newly Registered Residents Table -->
                <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 30px;">
                    <div style="padding: 16px 20px; border-bottom: 1px solid #e3e8e1; display: flex; justify-content: space-between; align-items: center; background: #f8faf7;">
                        <div>
                            <h2 style="font-size: 16px; font-weight: 600; color: #1e3a29; margin: 0;">Newly Registered Residents</h2>
                            <p style="font-size: 12px; color: #69786b; margin: 2px 0 0;">Recent additions to the community registry</p>
                        </div>
                        <a href="{{ route('residents.index') }}" style="font-size: 12px; font-weight: 600; color: #276747; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                            View all residents <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                        </a>
                    </div>
                    <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="border-bottom: 1px solid #edf1eb; text-align: left; color: #5a6b5c; font-size: 12px;">
                                <th style="padding: 12px 20px;">Full Name</th>
                                <th style="padding: 12px 16px;">Household</th>
                                <th style="padding: 12px 16px;">Gender & Age</th>
                                <th style="padding: 12px 16px;">Address</th>
                                <th style="padding: 12px 16px;">Voter Status</th>
                                <th style="padding: 12px 20px; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentResidents as $resident)
                                <tr style="border-bottom: 1px solid #edf1eb;">
                                    <td style="padding: 12px 20px; font-weight: 600; color: #1e3a29;">
                                        <a href="{{ route('residents.show', $resident) }}" style="color: inherit; text-decoration: underline;">
                                            {{ $resident->full_name }}
                                        </a>
                                        @if ($resident->contact_number)
                                            <div style="font-size: 11px; color: #788577; font-weight: 400; margin-top: 2px;">
                                                <i data-lucide="phone" style="width: 11px; height: 11px; display: inline;"></i> {{ $resident->contact_number }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        @if ($resident->household)
                                            <a href="{{ route('households.show', $resident->household) }}" style="color: #276747; text-decoration: none; font-weight: 600;">
                                                {{ $resident->household->household_number }}
                                            </a>
                                        @else
                                            <span style="color: #999; font-style: italic;">No Household</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 16px; color: #4b584e;">
                                        {{ $resident->gender }}, {{ $resident->birthdate ? $resident->birthdate->age . ' yrs' : 'N/A' }}
                                    </td>
                                    <td style="padding: 12px 16px; color: #556658; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $resident->address }}">
                                        {{ $resident->address }}
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        @if ($resident->is_voter)
                                            <span style="background: #eaf5eb; color: #236539; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Voter</span>
                                        @else
                                            <span style="background: #f4f4f4; color: #888; padding: 2px 8px; border-radius: 4px; font-size: 11px;">Non-Voter</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 20px; text-align: right;">
                                        <a href="{{ route('residents.show', $resident) }}" style="color: #276747; text-decoration: none; font-size: 12px; font-weight: 600; padding: 3px 8px; border: 1px solid #c8d8c9; border-radius: 4px;">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 28px; color: #829283;">
                                        No resident records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <footer class="workspace-footer" style="padding-block: 20px; color: #829283; font-size: 12px; display: flex; justify-content: space-between; border-top: 1px solid #e1e7de;">
                    <span>&copy; {{ date('Y') }} Barangay Information System</span>
                    <span>Administrative Management Portal</span>
                </footer>
            </main>
        </div>
    </body>
</html>
