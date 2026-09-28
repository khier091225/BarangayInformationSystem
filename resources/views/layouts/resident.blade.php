<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#276747">
        <link rel="icon" type="image/jpeg" href="{{ asset('images/Logo_kay-anlog.jpg') }}">
        <title>@yield('title', 'Resident Portal | Barangay Information System')</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="resident-body">
        <a class="skip-link" href="#resident-main">Skip to content</a>
        <aside id="resident-navigation" class="resident-app-sidebar" aria-label="Resident sidebar">
            <div class="resident-app-sidebar-inner">
                <div class="resident-sidebar-head">
                    <a class="resident-brand resident-sidebar-brand" href="{{ route('account') }}" aria-label="Barangay Kay-Anlog, resident dashboard">
                        <x-brand-seal />
                        <span><strong>Barangay<br>Kay-Anlog</strong><small>INFORMATION SYSTEM</small></span>
                    </a>
                    <button type="button" class="resident-sidebar-close" data-resident-sidebar-close aria-label="Close navigation"><i data-lucide="x" aria-hidden="true"></i></button>
                </div>

                <div class="resident-sidebar-label">RESIDENT PORTAL</div>
                <nav class="resident-side-nav" aria-label="Resident navigation">
                    <a href="{{ route('account') }}" @if (request()->routeIs('account')) aria-current="page" @endif><i data-lucide="layout-dashboard" aria-hidden="true"></i><span>Dashboard</span></a>

                    @if (auth()->user()->role === 'resident' && auth()->user()->resident_id !== null)
                        <span class="resident-nav-label">SERVICES</span>
                        <a href="{{ route('account.requests.certificate.create') }}" @if (request()->routeIs('account.requests.certificate.create')) aria-current="page" @endif><i data-lucide="files" aria-hidden="true"></i><span>Request a document</span></a>
                        <a href="{{ route('account.requests.blotter.create') }}" @if (request()->routeIs('account.requests.blotter.create')) aria-current="page" @endif><i data-lucide="notebook-pen" aria-hidden="true"></i><span>File a blotter</span></a>
                        <a href="{{ route('account.incidents.create') }}" @if (request()->routeIs('account.incidents.create')) aria-current="page" @endif><i data-lucide="message-square-warning" aria-hidden="true"></i><span>Report an incident</span></a>

                        <span class="resident-nav-label">ACTIVITY</span>
                        <a href="{{ route('account.requests.index') }}" @if (request()->routeIs('account.requests.index', 'account.requests.show')) aria-current="page" @endif><i data-lucide="inbox" aria-hidden="true"></i><span>My requests</span></a>
                        <a href="{{ route('account.incidents.index') }}" @if (request()->routeIs('account.incidents.index', 'account.incidents.show')) aria-current="page" @endif><i data-lucide="clipboard-list" aria-hidden="true"></i><span>My reports</span></a>

                        <span class="resident-nav-label">ACCOUNT</span>
                        <a href="{{ route('account.profile.edit') }}" @if (request()->routeIs('account.profile.*')) aria-current="page" @endif><i data-lucide="user-round" aria-hidden="true"></i><span>My profile</span></a>
                    @elseif (auth()->user()->role === 'staff')
                        <a href="{{ route('dashboard') }}"><i data-lucide="panel-left" aria-hidden="true"></i><span>Staff workspace</span></a>
                    @endif
                </nav>

                <div class="resident-sidebar-bottom">
                    <div class="resident-sidebar-user">
                        <span class="resident-sidebar-avatar" aria-hidden="true"><i data-lucide="user-round"></i></span>
                        <span><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role === 'staff' ? 'Barangay staff' : (auth()->user()->resident_id === null ? 'Verification needed' : 'Verified resident') }}</small></span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="resident-logout"><i data-lucide="log-out" aria-hidden="true"></i> Log out</button>
                    </form>
                </div>
            </div>
        </aside>
        <div class="resident-sidebar-backdrop" data-resident-sidebar-backdrop hidden aria-hidden="true"></div>

        <div class="resident-app-shell">
            <header class="resident-header">
                <div class="resident-header-inner">
                    <button type="button" class="resident-menu-toggle" data-resident-sidebar-toggle aria-label="Open resident navigation" aria-controls="resident-navigation" aria-expanded="false"><i data-lucide="menu" aria-hidden="true"></i><span>Menu</span></button>
                    <a class="resident-brand" href="{{ route('account') }}" aria-label="Barangay Kay-Anlog, resident dashboard">
                        <x-brand-seal />
                        <span><strong>Barangay Kay-Anlog</strong><small>RESIDENT PORTAL</small></span>
                    </a>
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
        </div>
        <x-chatbot-widget />
    </body>
</html>
