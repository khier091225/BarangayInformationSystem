<div>
    <!-- He who is contented is rich. - Laozi -->
</div>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#104f48">
        <title>@yield('title') | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-page">
        <a class="skip-link" href="#auth-main">Skip to form</a>
        <div class="auth-shell">
            <aside class="auth-story" aria-label="About the Barangay Information System">
                <a href="{{ route('login') }}" class="auth-brand" aria-label="Barangay Information System home">
                    <span class="auth-brand-mark"><i data-lucide="landmark" aria-hidden="true"></i></span>
                    <span>Barangay <strong>Information System</strong></span>
                </a>

                <div class="auth-story-content">
                    <div class="auth-story-kicker"><span></span> YOUR COMMUNITY, CONNECTED</div>
                    <h2>@yield('story-title')</h2>
                    <p class="auth-story-description">@yield('story-description')</p>
                    <div class="auth-story-detail">@yield('story-detail')</div>
                </div>

                <p class="auth-story-footer">For barangay staff and verified residents</p>
            </aside>

            <main id="auth-main" class="auth-main" tabindex="-1">
                <a href="{{ route('login') }}" class="auth-mobile-brand" aria-label="Barangay Information System home">
                    <span class="auth-brand-mark"><i data-lucide="landmark" aria-hidden="true"></i></span>
                    <span>Barangay <strong>Information System</strong></span>
                </a>

                <div class="auth-panel">
                    @yield('content')
                </div>
                <p class="auth-main-footer">Barangay Information System · Secure access for your community</p>
            </main>
        </div>
    </body>
</html>
