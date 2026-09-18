<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Barangay Officials | Barangay Information System</title>
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
                <a href="{{ route('blotters.index') }}"><i data-lucide="notebook-pen"></i> Blotter records</a>
                <a href="{{ route('officials.index') }}" class="selected"><i data-lucide="badge-check"></i> Officials</a>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="workspace-shell">
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb">
                    <span>Workspace</span>
                    <i data-lucide="chevron-right"></i>
                    <strong>Barangay Officials</strong>
                </div>
            </header>

            <main class="workspace-main" style="padding: 24px 34px;">
                <!-- Success Alert -->
                @if (session('success'))
                    <div style="background: #eaf5eb; color: #245838; border: 1px solid #c2e2c7; padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="workspace-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div>
                        <div class="eyebrow">BARANGAY LEADERSHIP</div>
                        <h1 style="font-size: 26px; color: #1e3a29; margin-top: 4px;">Barangay Officials</h1>
                        <p style="color: #69786b; font-size: 13px;">Manage elective and appointed community leaders, roles, and service terms.</p>
                    </div>
                    <a href="{{ route('officials.create') }}" class="button button-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i data-lucide="user-plus"></i> Add Official
                    </a>
                </div>

                <!-- Search & Filters -->
                <form method="GET" action="{{ route('officials.index') }}" style="display: flex; gap: 12px; margin-bottom: 20px;">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by name, position, or contact..." style="flex: 1; padding: 10px 14px; border: 1px solid #d4dcd2; border-radius: 6px; font-size: 13px;">
                    
                    <select name="position" style="padding: 10px 14px; border: 1px solid #d4dcd2; border-radius: 6px; font-size: 13px; background: white;">
                        <option value="">All Positions</option>
                        <option value="Barangay Captain" {{ request('position') == 'Barangay Captain' ? 'selected' : '' }}>Barangay Captain</option>
                        <option value="Barangay Kagawad" {{ request('position') == 'Barangay Kagawad' ? 'selected' : '' }}>Barangay Kagawad</option>
                        <option value="SK Chairman" {{ request('position') == 'SK Chairman' ? 'selected' : '' }}>SK Chairman</option>
                        <option value="Barangay Secretary" {{ request('position') == 'Barangay Secretary' ? 'selected' : '' }}>Barangay Secretary</option>
                        <option value="Barangay Treasurer" {{ request('position') == 'Barangay Treasurer' ? 'selected' : '' }}>Barangay Treasurer</option>
                    </select>

                    <button type="submit" class="button button-outline" style="cursor: pointer;">Filter</button>
                    @if(request('search') || request('position'))
                        <a href="{{ route('officials.index') }}" class="button" style="text-decoration: none; padding: 10px 14px; color: #666;">Reset</a>
                    @endif
                </form>

                <!-- Data Table -->
                <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8faf7; border-bottom: 1px solid #e3e8e1; text-align: left; color: #5a6b5c;">
                                <th style="padding: 14px 16px;">Official Name</th>
                                <th style="padding: 14px 16px;">Position</th>
                                <th style="padding: 14px 16px;">Contact Number</th>
                                <th style="padding: 14px 16px;">Term of Office</th>
                                <th style="padding: 14px 16px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($officials as $official)
                                <tr style="border-bottom: 1px solid #edf1eb;">
                                    <td style="padding: 14px 16px; font-weight: 600; color: #1e3a29;">
                                        {{ $official->name }}
                                    </td>
                                    <td style="padding: 14px 16px;">
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600;
                                            @if($official->position == 'Barangay Captain') background: #eaf5eb; color: #1e5e34;
                                            @elseif($official->position == 'SK Chairman') background: #eef3fc; color: #1a4f9c;
                                            @elseif($official->position == 'Barangay Kagawad') background: #fdf8e9; color: #876211;
                                            @else background: #f3f5f3; color: #465548; @endif">
                                            {{ $official->position }}
                                        </span>
                                    </td>
                                    <td style="padding: 14px 16px; color: #556658;">
                                        {{ $official->contact_number ?: 'Not provided' }}
                                    </td>
                                    <td style="padding: 14px 16px; color: #556658;">
                                        @if($official->term_start && $official->term_end)
                                            {{ $official->term_start->format('M Y') }} – {{ $official->term_end->format('M Y') }}
                                        @elseif($official->term_start)
                                            Since {{ $official->term_start->format('M Y') }}
                                        @else
                                            <span style="color: #999;">Active</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 16px; text-align: right;">
                                        <div style="display: inline-flex; gap: 8px;">
                                            <a href="{{ route('officials.edit', $official) }}" style="color: #556658; text-decoration: none; font-size: 12px; font-weight: 600; padding: 4px 8px; border: 1px solid #ccd5c8; border-radius: 4px;">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('officials.destroy', $official) }}" onsubmit="return confirm('Are you sure you want to remove this official?');" style="display: inline;">
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
                                    <td colspan="5" style="text-align: center; padding: 40px; color: #829283;">
                                        No barangay officials registered yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 20px;">
                    {{ $officials->links() }}
                </div>
            </main>
        </div>
    </body>
</html>