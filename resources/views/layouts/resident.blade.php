<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#276747">
        <title>@yield('title', 'Resident Portal | Barangay Information System')</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="resident-body">
        <a class="skip-link" href="#resident-main">Skip to content</a>
        <header class="resident-header">
            <div class="resident-header-inner">
                <a class="resident-brand" href="{{ route('account') }}" aria-label="Barangay Kay-Anlog, dashboard">
                    <x-brand-seal />
                    <span><strong>Barangay Kay-Anlog</strong><small>RESIDENT PORTAL</small></span>
                </a>

                <nav class="resident-nav" aria-label="Resident navigation">
                    <a href="{{ route('account') }}" @if (request()->routeIs('account')) aria-current="page" @endif><i data-lucide="layout-dashboard" aria-hidden="true"></i> Dashboard</a>
                    @if (auth()->user()->role === 'resident' && auth()->user()->resident_id !== null)
                        <a href="{{ route('account.requests.index') }}" @if (request()->routeIs('account.requests.index', 'account.requests.show')) aria-current="page" @endif><i data-lucide="files" aria-hidden="true"></i> My requests</a>
                        <a href="{{ route('account.profile.edit') }}" @if (request()->routeIs('account.profile.*')) aria-current="page" @endif><i data-lucide="user-round" aria-hidden="true"></i> My profile</a>
                    @endif
                </nav>

                <div class="resident-header-actions">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="resident-logout" aria-label="Log out"><i data-lucide="log-out" aria-hidden="true"></i> Log out</button>
                    </form>
                </div>
            </div>
        </header>

        <main id="resident-main" class="resident-main @yield('main-class')" tabindex="-1">
            @if (session('warning'))
                <div role="alert" class="resident-alert resident-alert-warning"><i data-lucide="info" aria-hidden="true"></i><span>{{ session('warning') }}</span></div>
            @endif
            @if (session('success'))
                <div role="status" class="resident-alert resident-alert-success"><i data-lucide="badge-check" aria-hidden="true"></i><span>{{ session('success') }}</span></div>
            @endif
            @yield('content')
            <footer class="resident-footer"><span>Barangay Kay-Anlog Information System</span><span>Services for your community</span></footer>
        </main>
    </body>
</html>
