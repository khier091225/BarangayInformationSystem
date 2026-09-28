<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#276747">
        <title>@yield('title') | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-page">
        <a class="skip-link" href="#auth-main">Skip to form</a>
        <div class="auth-shell">
            <aside class="auth-story" aria-label="About the Barangay Information System">
                <a href="{{ route('home') }}" class="auth-brand" aria-label="Barangay Information System home">
                    <span class="auth-brand-mark"><i data-lucide="landmark" aria-hidden="true"></i></span>
                    <span>Barangay <strong>Kay-Anlog</strong></span>
                </a>

                <div class="auth-story-content">
                    <div class="auth-story-kicker"><span></span> YOUR COMMUNITY, CONNECTED</div>
                    <h2>@yield('story-title')</h2>
                    <p class="auth-story-description">@yield('story-description')</p>
                    <div class="auth-story-detail">@yield('story-detail')</div>
                </div>

                <p class="auth-story-footer">
                    <a href="{{ route('home') }}" style="color: rgba(255,255,255,0.85); display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8125rem;">
                        <i data-lucide="arrow-left" style="width: 0.875rem; height: 0.875rem;"></i>
                        <span>Return to Public Homepage</span>
                    </a>
                </p>
            </aside>

            <main id="auth-main" class="auth-main" tabindex="-1">
                <a href="{{ route('home') }}" class="auth-mobile-brand" aria-label="Barangay Information System home">
                    <span class="auth-brand-mark"><i data-lucide="landmark" aria-hidden="true"></i></span>
                    <span>Barangay <strong>Kay-Anlog</strong></span>
                </a>

                <div class="auth-panel">
                    @yield('content')
                </div>
                <div class="auth-main-footer">
                    <div>Barangay Information System &middot; Secure access for your community</div>
                    <div class="lg:hidden mt-2">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-emerald-700 transition-colors font-medium">
                            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                            <span>Return to Public Homepage</span>
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
