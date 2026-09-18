<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Residents | Barangay Information System</title>
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
                <a href="{{ route('dashboard') }}#certificates"><i data-lucide="files"></i> Certificates</a>
                <a href="{{ route('blotters.index') }}"><i data-lucide="notebook-pen"></i> Blotter records</a>
                <a href="{{ route('officials.index') }}"><i data-lucide="badge-check"></i> Officials</a>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="workspace-shell">
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb">
                    <span>Workspace</span>
                    <i data-lucide="chevron-right"></i>
                    <strong>Residents</strong>
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
                        <div class="eyebrow">BARANGAY DIRECTORY</div>
                        <h1 style="font-size: 26px; color: #1e3a29; margin-top: 4px;">Resident Registry</h1>
                        <p style="color: #69786b; font-size: 13px;">Manage community residents, demographics, and voter registration records.</p>
                    </div>
                    <a href="{{ route('residents.create') }}" class="button button-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i data-lucide="user-plus"></i> Register Resident
                    </a>
                </div>

                <!-- Search & Filters -->
                <form method="GET" action="{{ route('residents.index') }}" style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by name, address, or contact..." style="flex: 1; min-width: 240px; padding: 10px 14px; border: 1px solid #d4dcd2; border-radius: 6px; font-size: 13px;">
                    
                    <select name="gender" style="padding: 10px 14px; border: 1px solid #d4dcd2; border-radius: 6px; font-size: 13px; background: white;">
                        <option value="">All Genders</option>
                        <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>

                    <select name="is_voter" style="padding: 10px 14px; border: 1px solid #d4dcd2; border-radius: 6px; font-size: 13px; background: white;">
                        <option value="">All Voters</option>
                        <option value="1" {{ request('is_voter') === '1' ? 'selected' : '' }}>Registered Voters</option>
                        <option value="0" {{ request('is_voter') === '0' ? 'selected' : '' }}>Non-Voters</option>
                    </select>

                    <button type="submit" class="button button-outline" style="cursor: pointer;">Filter</button>
                    @if(request('search') || request('gender') || request('is_voter') !== null)
                        <a href="{{ route('residents.index') }}" class="button" style="text-decoration: none; padding: 10px 14px; color: #666;">Reset</a>
                    @endif
                </form>

                <!-- Data Table -->
                <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <table class="workspace-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8faf7; border-bottom: 1px solid #e3e8e1; text-align: left; color: #5a6b5c;">
                                <th style="padding: 14px 16px;">Full Name</th>
                                <th style="padding: 14px 16px;">Household</th>
                                <th style="padding: 14px 16px;">Gender & Age</th>
                                <th style="padding: 14px 16px;">Civil Status</th>
                                <th style="padding: 14px 16px;">Address</th>
                                <th style="padding: 14px 16px;">Voter</th>
                                <th style="padding: 14px 16px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($residents as $resident)
                                <tr style="border-bottom: 1px solid #edf1eb;">
                                    <td style="padding: 14px 16px; font-weight: 600; color: #1e3a29;">
                                        <a href="{{ route('residents.show', $resident) }}" style="color: inherit; text-decoration: underline;">
                                            {{ $resident->full_name }}
                                        </a>
                                        @if($resident->contact_number)
                                            <div style="font-size: 11px; color: #788577; font-weight: 400; margin-top: 2px;">
                                                <i data-lucide="phone" style="width: 11px; height: 11px; display: inline;"></i> {{ $resident->contact_number }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 16px;">
                                        @if($resident->household)
                                            <a href="{{ route('households.show', $resident->household) }}" style="color: #276747; text-decoration: none; font-weight: 600;">
                                                {{ $resident->household->household_number }}
                                            </a>
                                            <div style="font-size: 11px; color: #788577;">Head: {{ $resident->household->household_head }}</div>
                                        @else
                                            <span style="color: #999; font-style: italic;">No Household</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 16px;">
                                        <span>{{ $resident->gender }}</span>,
                                        <span style="color: #556658;">
                                            {{ $resident->birthdate ? $resident->birthdate->age . ' yrs old' : 'N/A' }}
                                        </span>
                                    </td>
                                    <td style="padding: 14px 16px; color: #4b584e;">
                                        {{ $resident->civil_status }}
                                    </td>
                                    <td style="padding: 14px 16px; color: #556658; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $resident->address }}">
                                        {{ $resident->address }}
                                    </td>
                                    <td style="padding: 14px 16px;">
                                        @if ($resident->is_voter)
                                            <span style="background: #eaf5eb; color: #236539; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Voter</span>
                                        @else
                                            <span style="background: #f4f4f4; color: #888; padding: 3px 8px; border-radius: 4px; font-size: 11px;">Non-Voter</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 16px; text-align: right;">
                                        <div style="display: inline-flex; gap: 8px;">
                                            <a href="{{ route('residents.show', $resident) }}" style="color: #276747; text-decoration: none; font-size: 12px; font-weight: 600; padding: 4px 8px; border: 1px solid #c8d8c9; border-radius: 4px;">
                                                View
                                            </a>
                                            <a href="{{ route('residents.edit', $resident) }}" style="color: #556658; text-decoration: none; font-size: 12px; font-weight: 600; padding: 4px 8px; border: 1px solid #ccd5c8; border-radius: 4px;">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('residents.destroy', $resident) }}" onsubmit="return confirm('Are you sure you want to delete this resident record?');" style="display: inline;">
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
                                        No resident records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 20px;">
                    {{ $residents->links() }}
                </div>
            </main>
        </div>
    </body>
</html>

