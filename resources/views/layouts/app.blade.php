<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#276747">
        <title>@yield('title', 'Barangay Information System')</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="workspace-page">
        <a class="skip-link" href="#workspace-main">Skip to content</a>
        <!-- Sidebar Navigation -->
        <aside class="workspace-sidebar" id="workspace-navigation" aria-label="Workspace navigation">
            <a href="{{ route('dashboard') }}" class="brand workspace-brand">
                <span class="brand-mark"><i data-lucide="landmark"></i></span>
                <span class="brand-name">Barangay Kay-Anlog<span>INFORMATION SYSTEM</span></span>
            </a>
            <div class="workspace-label">STAFF WORKSPACE</div>
            <nav class="workspace-nav" aria-label="Main workspace">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="layout-dashboard">Overview</x-nav-link>
                <span class="nav-group-label">BARANGAY MANAGEMENT</span>
                <x-nav-link :href="route('residents.index')" :active="request()->routeIs('residents.*')" icon="users-round">Residents</x-nav-link>
                <x-nav-link :href="route('service-requests.index')" :active="request()->routeIs('service-requests.*')" icon="inbox">Resident requests</x-nav-link>
                <x-nav-link :href="route('households.index')" :active="request()->routeIs('households.*')" icon="house">Households</x-nav-link>
                <x-nav-link :href="route('certificates.index')" :active="request()->routeIs('certificates.*')" icon="files">Certificates</x-nav-link>
                <x-nav-link :href="route('blotters.index')" :active="request()->routeIs('blotters.*')" icon="notebook-pen">Blotter records</x-nav-link>
                <x-nav-link :href="route('officials.index')" :active="request()->routeIs('officials.*')" icon="badge-check">Officials</x-nav-link>
                <span class="nav-group-label">ACCOUNT</span>
                <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="user-round">My profile</x-nav-link>
            </nav>
            <div class="sidebar-bottom">
                <a href="{{ route('home') }}" class="public-site-link" target="_blank" title="View Public Portal">
                    <i data-lucide="globe"></i>
                    <span>Public Portal</span>
                    <i data-lucide="arrow-up-right"></i>
                </a>
                <div class="sidebar-user-card">
                    <a href="{{ route('profile.edit') }}" class="sidebar-profile" aria-label="Manage your profile">
                        <div class="staff-avatar-wrapper">
                            <span class="staff-avatar" aria-hidden="true"><i data-lucide="user-round"></i></span>
                        </div>
                        <div class="sidebar-user-meta">
                            <strong class="sidebar-user-name">{{ auth()->user()->name }}</strong>
                            <span class="sidebar-user-role">
                                <i data-lucide="badge-check" aria-hidden="true"></i> Barangay staff
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        </aside>
        <div class="sidebar-backdrop" hidden></div>

        <div class="workspace-shell">
            @section('topbar')
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb">
                    <button type="button" class="icon-button sidebar-toggle" aria-label="Open sidebar" aria-controls="workspace-navigation" aria-expanded="false">
                        <i data-lucide="panel-left" aria-hidden="true"></i>
                    </button>
                    @yield('breadcrumb')
                </div>
                <div class="topbar-actions">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="topbar-logout-btn" aria-label="Log out">
                            <i data-lucide="log-out" aria-hidden="true"></i>
                            <span>Log out</span>
                        </button>
                    </form>
                </div>
            </header>
            @show

            <main class="workspace-main" id="workspace-main" tabindex="-1" style="@yield('main-style')">
                @if (session('success'))
                    <div class="no-print" role="status" style="background: var(--success-soft); color: var(--accent-hover); border: 1px solid var(--success-line); padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('warning'))
                    <div class="no-print" role="alert" style="background: var(--warning-soft); color: var(--warning); border: 1px solid var(--warning-line); padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">
                        {{ session('warning') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
