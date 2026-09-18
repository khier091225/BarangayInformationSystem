<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#164f3d">
        <title>Staff Login | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="login-page">
        <img class="login-background" src="{{ asset('images/community.jpg') }}" alt="" width="1800" height="1000">
        <header class="login-header">
            <a href="{{ route('home') }}" class="brand"><span class="brand-mark"><i data-lucide="landmark" aria-hidden="true"></i></span><span class="brand-name">Barangay<span>INFORMATION SYSTEM</span></span></a>
            <a href="{{ route('home') }}" class="login-back"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to website</a>
        </header>
        <main class="login-main">
            <section class="login-panel" aria-labelledby="login-heading">
                <span class="login-emblem"><i data-lucide="landmark" aria-hidden="true"></i></span>
                <div class="eyebrow">BARANGAY STAFF PORTAL</div>
                <h1 id="login-heading">Welcome back.</h1>
                <p class="login-intro">A better day of service starts here.<br>Your barangay workspace is ready.</p>
                <div class="login-identity"><span class="staff-avatar"><i data-lucide="user-round" aria-hidden="true"></i></span><span><strong>Barangay staff</strong><small>Demo workspace</small></span><span class="identity-indicator"><i data-lucide="check" aria-hidden="true"></i></span></div>
                <form method="POST" action="{{ route('demo.enter') }}">
                    @csrf
                    <button type="submit" class="button button-primary login-submit">Enter dashboard <i data-lucide="arrow-right" aria-hidden="true"></i></button>
                </form>
                <p class="login-note"><i data-lucide="info" aria-hidden="true"></i> No credentials required. Sample data only.</p>
                <div class="login-panel-footer"><i data-lucide="hand-heart" aria-hidden="true"></i> Local service. Lasting impact.</div>
            </section>
        </main>
        <footer class="login-footer"><span>&copy; {{ date('Y') }} Barangay Information System</span><span>For the people. For the community.</span></footer>
    </body>
</html>
