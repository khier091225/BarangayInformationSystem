<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Blotter Records | Barangay Information System</title>
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
                <a href="{{ route('certificates.index') }}"><i data-lucide="files"></i> Certificates</a>
                <a href="{{ route('blotters.index') }}" class="selected"><i data-lucide="notebook-pen"></i> Blotter records</a>
                <a href="{{ route('officials.index') }}"><i data-lucide="badge-check"></i> Officials</a>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="workspace-shell">
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb">
                    <span>Workspace</span>
                    <i data-lucide="chevron-right"></i>
                    <strong>Blotter Records</strong>
                </div>
            </header>

            <main class="workspace-main" style="padding: 24px 34px;">
                <!-- Success Message Alert -->
                @if (session('success'))
                    <div style="background: #eaf5eb; color: #245838; border: 1px solid #c2e2c7; padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="workspace-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div>
                        <div class="eyebrow">PEACE & ORDER</div>
                        <h1 style="font-size: 26px; color: #1e3a29; margin-top: 4px;">Blotter Case Register</h1>
                        <p style="color: #69786b; font-size: 13px;">Manage community disputes, complaints, and hearing records.</p>
                    </div>
                    <a href="{{ route('blotters.create') }}" class="button button-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i data-lucide="plus"></i> File New Complaint
                    </a>
                </div>

                <!-- Search & Filters -->
                <form method="GET" action="{{ route('blotters.index') }}" style="display: flex; gap: 12px; margin-bottom: 20px;">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search complainant, respondent, or incident..." style="flex: 1; padding: 10px 14px; border: 1px solid #d4dcd2; border-radius: 6px; font-size: 13px;">
                    
                    <select name="status" style="padding: 10px 14px; border: 1px solid #d4dcd2; border-radius: 6px; font-size: 13px; background: white;">
                        <option value="">All Statuses</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Settled" {{ request('status') == 'Settled' ? 'selected' : '' }}>Settled</option>
                        <option value="Dismissed" {{ request('status') == 'Dismissed' ? 'selected' : '' }}>Dismissed</option>
                    </select>

                    <button type="submit" class="button button-outline" style="cursor: pointer;">Filter</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('blotters.index') }}" class="button" style="text-decoration: none; padding: 10px 14px; color: #666;">Reset</a>
                    @endif
                </form>

                <!-- Data Table -->
                <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8faf7; border-bottom: 1px solid #e3e8e1; text-align: left; color: #5a6b5c;">
                                <th style="padding: 14px 16px;">Case #</th>
                                <th style="padding: 14px 16px;">Complainant</th>
                                <th style="padding: 14px 16px;">Respondent</th>
                                <th style="padding: 14px 16px;">Incident Details</th>
                                <th style="padding: 14px 16px;">Date</th>
                                <th style="padding: 14px 16px;">Status</th>
                                <th style="padding: 14px 16px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($blotters as $blotter)
                                <tr style="border-bottom: 1px solid #edf1eb;">
                                    <td style="padding: 14px 16px; font-weight: 600; color: #1e3a29;">#{{ $blotter->id }}</td>
                                    <td style="padding: 14px 16px; font-weight: 600;">{{ $blotter->complainant }}</td>
                                    <td style="padding: 14px 16px; color: #6d4038;">{{ $blotter->respondent }}</td>
                                    <td style="padding: 14px 16px; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $blotter->incident }}">
                                        {{ $blotter->incident }}
                                    </td>
                                    <td style="padding: 14px 16px; color: #667568;">
                                        {{ $blotter->incident_date ? $blotter->incident_date->format('M d, Y') : '-' }}
                                    </td>
                                    <td style="padding: 14px 16px;">
                                        <span style="display: inline-block; padding: 4px 9px; border-radius: 4px; font-size: 11px; font-weight: 600;
                                            @if($blotter->status == 'Pending') background: #fdf3d8; color: #875c10;
                                            @elseif($blotter->status == 'Settled') background: #eaf5eb; color: #236539;
                                            @else background: #f5eaea; color: #822727; @endif">
                                            {{ $blotter->status }}
                                        </span>
                                    </td>
                                    <td style="padding: 14px 16px; text-align: right;">
                                        <div style="display: inline-flex; gap: 8px;">
                                            <a href="{{ route('blotters.edit', $blotter) }}" style="color: #276747; text-decoration: none; font-size: 12px; font-weight: 600; padding: 4px 8px; border: 1px solid #c8d8c9; border-radius: 4px;">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('blotters.destroy', $blotter) }}" onsubmit="return confirm('Are you sure you want to delete this blotter record?');" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: none; border: 1px solid #eed0ce; color: #a43229; font-size: 12px; font-weight: 600; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px; color: #829283;">
                                        No blotter records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 20px;">
                    {{ $blotters->links() }}
                </div>
            </main>
        </div>
    </body>
</html>