<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Explore barangay service guidance, sample community notices, and a demonstration staff workspace.">
        <meta name="theme-color" content="#11665e">
        <title>Home | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="public-page">
        <a class="skip-link" href="#main">Skip to content</a>
        <div class="utility-bar">
            <div class="container utility-inner"><i data-lucide="info" aria-hidden="true"></i><span>Demonstration website. Service guidance and notices are for illustration.</span></div>
        </div>
        <header class="site-header">
            <div class="container header-inner">
                <a href="{{ route('home') }}" class="brand" aria-label="Barangay Information System home">
                    <span class="brand-mark"><i data-lucide="landmark" aria-hidden="true"></i></span>
                    <span class="brand-name">Barangay<span>Information System</span></span>
                </a>
                <button class="menu-toggle icon-button" type="button" aria-label="Open navigation" aria-controls="primary-navigation" aria-expanded="false" title="Open navigation"><i data-lucide="menu" aria-hidden="true"></i></button>
                <nav id="primary-navigation" class="primary-navigation" aria-label="Main navigation">
                    <a class="nav-link" href="#services">Services</a>
                    <a class="nav-link" href="#announcements">Noticeboard</a>
                    <a class="nav-link" href="#about">About this site</a>
                    <a class="button button-outline nav-action" href="{{ route('login') }}">Demo workspace <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                </nav>
            </div>
        </header>
        <main id="main" tabindex="-1">
            <section class="public-intro" aria-labelledby="hero-heading">
                <div class="container intro-layout">
                    <div class="intro-copy">
                        <h1 id="hero-heading">Local services.<br>A clearer place to start.</h1>
                        <p>Find guidance for everyday barangay documents and community concerns. Choose a service below to learn your next step.</p>
                        <a href="#services" class="button button-primary">Find a service <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                    </div>
                    <figure class="community-photo">
                        <img src="{{ asset('images/community.jpg') }}" alt="Homes beside green rice fields in Ligao City, Philippines" fetchpriority="high" width="1800" height="1000">
                        <figcaption><a href="https://unsplash.com/photos/WRBEzYe6TIU" target="_blank" rel="noopener noreferrer">Ligao City · Photo by Karlo King <i data-lucide="external-link" aria-hidden="true"></i><span class="sr-only"> (opens in a new tab)</span></a></figcaption>
                    </figure>
                </div>
            </section>
            <section id="services" class="services-section section-space" aria-labelledby="services-heading" tabindex="-1">
                <div class="container">
                    <div class="section-heading"><div><h2 id="services-heading">What do you need help with?</h2><p>Read the guidance before visiting your barangay office.</p></div><span class="service-availability"><i data-lucide="info" aria-hidden="true"></i> Information only. No online applications.</span></div>
                    <div class="service-grid">
                        @foreach ([
                            ['category' => 'Documents', 'title' => 'Barangay Clearance', 'description' => 'For employment, business, and other personal requirements.', 'detail' => 'Contact the barangay office to confirm the documents, fees, and processing time for your clearance. Online applications are not yet available.'],
                            ['category' => 'Certificates', 'title' => 'Certificate of Residency', 'description' => 'Proof of residence for applications and official transactions.', 'detail' => 'The barangay office can help verify your residency and explain the certification process. Online applications are not yet available.'],
                            ['category' => 'Assistance', 'title' => 'Certificate of Indigency', 'description' => 'Supporting documentation for financial, medical, or educational assistance.', 'detail' => 'Contact the barangay office for eligibility assessment and the current requirements for this certificate. Online applications are not yet available.'],
                            ['category' => 'Community support', 'title' => 'Blotter & Concerns', 'description' => 'Guidance on recording incidents and raising community concerns.', 'detail' => 'Visit the barangay office to discuss a concern or ask about recording an incident. This page does not submit or monitor reports.'],
                        ] as $service)
                            <button type="button" class="service-row" data-detail-title="{{ $service['title'] }}" data-detail-text="{{ $service['detail'] }}" data-detail-label="Resident service">
                                <span class="service-copy"><strong>{{ $service['title'] }}</strong><span class="service-description">{{ $service['description'] }}</span><span class="service-category">{{ $service['category'] }}</span></span>
                                <span class="service-link"><span>View guidance</span><i data-lucide="arrow-right" aria-hidden="true"></i></span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </section>
            <section id="announcements" class="announcements-section section-space" aria-labelledby="announcements-heading" tabindex="-1">
                <div class="container news-layout">
                    <div class="noticeboard">
                        <div class="section-heading"><div><h2 id="announcements-heading">Community noticeboard</h2><p>Example updates, not official announcements.</p></div><span class="sample-label">Sample notices</span></div>
                        <div class="notice-filters" role="group" aria-label="Filter community notices">
                            <button type="button" class="filter active" data-filter="all" aria-pressed="true">All updates</button>
                            <button type="button" class="filter" data-filter="advisory" aria-pressed="false">Advisories</button>
                            <button type="button" class="filter" data-filter="event" aria-pressed="false">Events</button>
                            <button type="button" class="filter" data-filter="program" aria-pressed="false">Programs</button>
                        </div>
                        <p class="sr-only" id="notice-filter-status" role="status">Showing all 3 sample notices.</p>
                        <div class="notice-list">
                            @foreach ([
                                ['type' => 'advisory', 'label' => 'Advisory', 'title' => 'Preparing for your next barangay visit', 'description' => 'Check office hours and document requirements before you go.', 'detail' => 'Before visiting, contact the barangay office to confirm office hours and the requirements for your transaction. This is sample homepage content, not an official advisory.'],
                                ['type' => 'event', 'label' => 'Community event', 'title' => 'Together for a cleaner community', 'description' => 'An example of a community cleanup and volunteer notice.', 'detail' => 'A space for upcoming community cleanups and volunteer activities. Dates and meeting locations will be published when confirmed. This is a sample notice.'],
                                ['type' => 'program', 'label' => 'Community program', 'title' => 'Making community wellness a priority', 'description' => 'An example of a local health and wellness program update.', 'detail' => 'A space for confirmed health programs and community wellness updates. Contact the barangay office for currently available services. This is a sample notice.'],
                            ] as $notice)
                                <button type="button" class="notice-item" data-notice-type="{{ $notice['type'] }}" data-detail-title="{{ $notice['title'] }}" data-detail-text="{{ $notice['detail'] }}" data-detail-label="Sample {{ strtolower($notice['label']) }}">
                                    <span class="notice-copy"><strong>{{ $notice['title'] }}</strong><span class="notice-description">{{ $notice['description'] }}</span><span class="notice-category">{{ $notice['label'] }}</span></span>
                                    <i class="notice-arrow" data-lucide="arrow-up-right" aria-hidden="true"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <aside class="visit-guidance" aria-labelledby="visit-heading">
                        <h2 id="visit-heading">Before you visit</h2>
                        <p>Requirements vary by service. Confirm these details with your own barangay office:</p>
                        <ul><li>Documents and identification to bring</li><li>Current fees and processing time</li><li>Office hours and where to apply</li></ul>
                        <p class="guidance-disclosure">This demo has no verified office address, contact number, or opening hours. It cannot accept requests or incident reports.</p>
                    </aside>
                </div>
            </section>
            <section id="about" class="about-section section-space" aria-labelledby="about-heading" tabindex="-1">
                <div class="container about-inner">
                    <div class="about-copy"><h2 id="about-heading">About this demonstration</h2><p>The Barangay Information System brings resident information and staff workflows together. This preview uses sample content and is not an official barangay service channel.</p></div>
                    <div class="about-workspace"><h3>Explore the staff side</h3><p>Try sample records, document requests, and community workflows. No account needed.</p><a class="text-link" href="{{ route('login') }}">Open demo workspace <i data-lucide="arrow-right" aria-hidden="true"></i></a></div>
                </div>
            </section>
        </main>
        <footer class="site-footer">
            <div class="container footer-inner"><a href="{{ route('home') }}" class="footer-brand"><i data-lucide="landmark" aria-hidden="true"></i> Barangay Information System</a><span>&copy; {{ date('Y') }} · Demonstration only</span><a href="#main">Back to top <i data-lucide="arrow-up" aria-hidden="true"></i></a></div>
        </footer>
        <dialog id="detail-dialog" aria-labelledby="detail-title" aria-describedby="detail-text">
            <div class="dialog-top"><h2 id="detail-title"></h2><button type="button" class="icon-button dialog-close" aria-label="Close details" title="Close details"><i data-lucide="x" aria-hidden="true"></i></button></div>
            <span class="sample-label" id="detail-label"></span><p id="detail-text"></p><button type="button" class="button button-primary dialog-close">Close <i data-lucide="check" aria-hidden="true"></i></button>
        </dialog>
    </body>
</html>
