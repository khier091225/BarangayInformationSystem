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
                <a href="{{ route('dashboard') }}" @class(['selected' => request()->routeIs('dashboard')]) @if (request()->routeIs('dashboard')) aria-current="page" @endif><i data-lucide="layout-dashboard"></i> Overview</a>
                <span class="nav-group-label">BARANGAY MANAGEMENT</span>
                <a href="{{ route('residents.index') }}" @class(['selected' => request()->routeIs('residents.*')]) @if (request()->routeIs('residents.*')) aria-current="page" @endif><i data-lucide="users-round"></i> Residents</a>
                <a href="{{ route('households.index') }}" @class(['selected' => request()->routeIs('households.*')]) @if (request()->routeIs('households.*')) aria-current="page" @endif><i data-lucide="house"></i> Households</a>
                <a href="{{ route('certificates.index') }}" @class(['selected' => request()->routeIs('certificates.*')]) @if (request()->routeIs('certificates.*')) aria-current="page" @endif><i data-lucide="files"></i> Certificates</a>
                <a href="{{ route('blotters.index') }}" @class(['selected' => request()->routeIs('blotters.*')]) @if (request()->routeIs('blotters.*')) aria-current="page" @endif><i data-lucide="notebook-pen"></i> Blotter records</a>
                <a href="{{ route('officials.index') }}" @class(['selected' => request()->routeIs('officials.*')]) @if (request()->routeIs('officials.*')) aria-current="page" @endif><i data-lucide="badge-check"></i> Officials</a>
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-profile">
                    <span class="staff-avatar" aria-hidden="true">BS</span>
                    <span><strong>Barangay Staff</strong><small>Authorized Portal</small></span>
                </div>
            </div>
        </aside>

        <div class="workspace-shell">
            @section('topbar')
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb">
                    @yield('breadcrumb')
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
    </body>
</html>
