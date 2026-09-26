<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#11665e">
        <title>@yield('title', 'Barangay Information System')</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
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
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="layout-dashboard">Overview</x-nav-link>
                <span class="nav-group-label">BARANGAY MANAGEMENT</span>
                <x-nav-link :href="route('residents.index')" :active="request()->routeIs('residents.*')" icon="users-round">Residents</x-nav-link>
                <x-nav-link :href="route('households.index')" :active="request()->routeIs('households.*')" icon="house">Households</x-nav-link>
                <x-nav-link :href="route('certificates.index')" :active="request()->routeIs('certificates.*')" icon="files">Certificates</x-nav-link>
                <x-nav-link :href="route('blotters.index')" :active="request()->routeIs('blotters.*')" icon="notebook-pen">Blotter records</x-nav-link>
                <x-nav-link :href="route('officials.index')" :active="request()->routeIs('officials.*')" icon="badge-check">Officials</x-nav-link>
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-user-card">
                    <div class="sidebar-profile">
                        <div class="staff-avatar-wrapper">
                            <span class="staff-avatar" aria-hidden="true">BS</span>
                            <span class="status-indicator-dot" aria-label="Session active"></span>
                        </div>
                        <div class="sidebar-user-meta">
                            <strong class="sidebar-user-name">{{ auth()->user()->name }}</strong>
                            <span class="sidebar-user-role">
                                <i data-lucide="badge-check" aria-hidden="true"></i> Signed-in staff
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        <div class="sidebar-backdrop" hidden></div>

        <div class="workspace-shell">
            @section('topbar')
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb">
                    @yield('breadcrumb')
                </div>
                <div class="topbar-actions">
                    <div class="topbar-session-badge" aria-label="Signed-in session active">
                        <span class="status-indicator-dot" aria-hidden="true"></span>
                        <span>Signed in</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="topbar-logout-btn" data-logout-trigger aria-label="Log out of session">
                            <i data-lucide="log-out" aria-hidden="true"></i>
                            <span>Log out</span>
                        </button>
                    </form>
                </div>
            </header>
            @show

            <main class="workspace-main" id="workspace-main" tabindex="-1" style="padding: 24px 34px; @yield('main-style')">
                @if (session('success'))
                    <div class="no-print" role="status" style="background: #eaf5eb; color: #245838; border: 1px solid #c2e2c7; padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        <!-- Logout Confirmation Modal -->
        <dialog class="logout-confirm-dialog" id="logout-confirm-dialog" aria-labelledby="logout-dialog-title" aria-describedby="logout-dialog-desc">
            <div class="logout-dialog-header">
                <div class="logout-dialog-icon">
                    <i data-lucide="log-out" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 id="logout-dialog-title">Log out of Dashboard?</h2>
                    <p id="logout-dialog-desc">You are about to exit the administrative portal. Any unsaved edits will be discarded.</p>
                </div>
            </div>
            <div class="logout-dialog-actions">
                <button type="button" class="logout-dialog-cancel-btn" id="cancel-logout-btn">Stay in Dashboard</button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-dialog-confirm-btn" id="confirm-logout-link">
                        <i data-lucide="log-out" aria-hidden="true"></i>
                        <span>Log out</span>
                    </button>
                </form>
            </div>
        </dialog>

        @stack('scripts')
    </body>
</html>
