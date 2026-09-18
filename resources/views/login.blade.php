<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#11665e">
        <title>Demo workspace | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="demo-entry-page">
        <a class="skip-link" href="#main">Skip to content</a>
        <header class="demo-entry-header container">
            <a href="{{ route('home') }}" class="brand" aria-label="Barangay Information System home"><span class="brand-mark"><i data-lucide="landmark" aria-hidden="true"></i></span><span class="brand-name">Barangay<span>Information System</span></span></a>
            <a href="{{ route('home') }}" class="text-link"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to website</a>
        </header>
        <main id="main" class="demo-entry-main container" tabindex="-1">
            <section class="demo-entry-panel" aria-labelledby="login-heading">
                <h1 id="login-heading">Demo workspace</h1>
                <p class="demo-entry-intro">Explore the everyday work of a barangay office, without an account.</p>
                <div class="demo-entry-description">
                    <p>Browse sample resident records, review document requests, and try community workflows.</p>
                    <ul>
                        <li><i data-lucide="check" aria-hidden="true"></i><span>This is a demonstration, not an authenticated staff session.</span></li>
                        <li><i data-lucide="check" aria-hidden="true"></i><span>Changes are temporary. Sample data resets when you reload the workspace.</span></li>
                        <li><i data-lucide="check" aria-hidden="true"></i><span>Use fictional information only. Nothing is sent to a barangay office.</span></li>
                    </ul>
                </div>
                <form method="POST" action="{{ route('demo.enter') }}" data-demo-entry>
                    @csrf
                    <button type="submit" class="button button-primary demo-entry-submit">Enter dashboard <i data-lucide="arrow-right" aria-hidden="true"></i></button>
                </form>
                <p class="demo-entry-note"><i data-lucide="info" aria-hidden="true"></i><span>No credentials required. Sample data only.</span></p>
            </section>
        </main>
        <footer class="demo-entry-footer container"><span>&copy; {{ date('Y') }} Barangay Information System</span><span>Demonstration only</span></footer>
    </body>
</html>
