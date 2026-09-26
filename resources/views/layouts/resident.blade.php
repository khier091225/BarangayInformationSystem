<div>
    <!-- He who is contented is rich. - Laozi -->
</div>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#11665e">
        <title>@yield('title', 'Resident Portal | Barangay Information System')</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .resident-body { margin: 0; background: #f3f7f3; color: #203629; }
            .resident-header { background: #fff; border-bottom: 1px solid #dce8dd; }
            .resident-header-inner { max-width: 1100px; margin: auto; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
            .resident-brand { color: #17593c; font-weight: 800; font-size: 18px; text-decoration: none; }
            .resident-nav { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
            .resident-nav a { color: #425c49; text-decoration: none; font-size: 13px; font-weight: 600; }
            .resident-nav a[aria-current="page"] { color: #126247; text-decoration: underline; text-underline-offset: 5px; }
            .resident-main { max-width: 1100px; margin: 0 auto; padding: 32px 24px 64px; }
            .resident-card { background: #fff; border: 1px solid #dce8dd; border-radius: 12px; padding: 24px; box-shadow: 0 3px 12px rgba(25, 72, 39, .04); }
            .resident-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
            .resident-heading { font-size: 28px; color: #183d2a; margin: 4px 0 8px; }
            .resident-muted { color: #607268; font-size: 13px; line-height: 1.5; }
            .resident-list { width: 100%; border-collapse: collapse; font-size: 13px; }
            .resident-list th, .resident-list td { padding: 13px 12px; border-bottom: 1px solid #e8eee8; text-align: left; vertical-align: top; }
            .resident-list th { color: #5a6f60; background: #f7faf7; font-size: 12px; }
            .resident-badge { display: inline-block; border-radius: 99px; padding: 4px 10px; font-size: 11px; font-weight: 700; background: #fdf2d8; color: #78520d; }
            .resident-badge-completed { background: #e5f5e8; color: #236339; }
            .resident-badge-declined { background: #f9e9e7; color: #9b3028; }
            @media (max-width: 640px) { .resident-main { padding: 24px 16px 48px; } .resident-card { padding: 18px; } .resident-list-wrap { overflow-x: auto; } }
        </style>
        @stack('styles')
    </head>
    <body class="resident-body">
        <header class="resident-header">
            <div class="resident-header-inner">
                <a class="resident-brand" href="{{ route('account') }}">Barangay Information System</a>
                <nav class="resident-nav" aria-label="Resident navigation">
                    <a href="{{ route('account') }}" @if (request()->routeIs('account')) aria-current="page" @endif>Dashboard</a>
                    @if (auth()->user()->role === 'resident' && auth()->user()->resident_id !== null)
                        <a href="{{ route('account.requests.index') }}" @if (request()->routeIs('account.requests.*')) aria-current="page" @endif>My requests</a>
                        <a href="{{ route('account.requests.certificate.create') }}">Request document</a>
                        <a href="{{ route('account.requests.blotter.create') }}">File blotter report</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="button button-outline">Log out</button>
                    </form>
                </nav>
            </div>
        </header>
        <main class="resident-main">
            @if (session('warning'))
                <div role="alert" class="resident-card" style="background: #fff7df; color: #704800; margin-bottom: 20px;">{{ session('warning') }}</div>
            @endif
            @if (session('success'))
                <div role="status" class="resident-card" style="background: #eaf5eb; color: #245838; margin-bottom: 20px;">{{ session('success') }}</div>
            @endif
            @yield('content')
        </main>
    </body>
</html>
