<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Your barangay's home for resident services, community announcements, and local information.">
        <meta name="theme-color" content="#164f3d">
        <title>Home | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <a class="skip-link" href="#main">Skip to content</a>
        <div class="utility-bar">
            <div class="container utility-inner">
                <span><i data-lucide="flag" aria-hidden="true"></i> A community connected. A barangay empowered.</span>
                <a href="#about">Serving our community <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
            </div>
        </div>
        <header class="site-header">
            <div class="container header-inner">
                <a href="{{ route('home') }}" class="brand" aria-label="Barangay Information System home">
                    <span class="brand-mark"><i data-lucide="landmark" aria-hidden="true"></i></span>
                    <span class="brand-name">Barangay<span>INFORMATION SYSTEM</span></span>
                </a>
                <button class="menu-toggle icon-button" type="button" aria-label="Open navigation" aria-controls="primary-navigation" aria-expanded="false" title="Open navigation"><i data-lucide="menu" aria-hidden="true"></i></button>
                <nav id="primary-navigation" class="primary-navigation" aria-label="Main navigation">
                    <a class="nav-link active" href="{{ route('home') }}" aria-current="page">Home</a>
                    <a class="nav-link" href="#services">Services</a>
                    <a class="nav-link" href="#announcements">Announcements</a>
                    <a class="nav-link" href="#about">About the barangay</a>
                    <a class="button button-primary nav-action" href="{{ route('login') }}">Staff login <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                </nav>
            </div>
        </header>
        <main id="main">
            <section class="hero" aria-labelledby="hero-heading">
                <img class="hero-image" src="{{ asset('images/community.jpg') }}" alt="A Philippine community surrounded by green rice fields in Ligao City" fetchpriority="high" width="1800" height="1000">
                <div class="hero-shade"></div>
                <div class="container hero-content">
                    <div class="eyebrow hero-eyebrow"><span></span> YOUR COMMUNITY, CONNECTED</div>
                    <h1 id="hero-heading">Barangay<br>Information System<span class="heading-period">.</span></h1>
                    <p>A simpler way to access local services, stay informed,<br class="desktop-break"> and take part in the community we call home.</p>
                    <div class="hero-actions">
                        <a href="#services" class="button button-gold">Explore services <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                        <a href="#announcements" class="hero-secondary">Community updates <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                    </div>
                    <div class="hero-bottom"><span><i data-lucide="map-pin" aria-hidden="true"></i> Rooted in community. Built for you.</span><a href="https://unsplash.com/photos/WRBEzYe6TIU" target="_blank" rel="noopener noreferrer">Ligao City, Philippines &middot; Photo by Karlo King <i data-lucide="external-link" aria-hidden="true"></i></a></div>
                </div>
            </section>
            <div class="quick-links">
                <div class="container quick-links-inner">
                    <a href="#services"><span class="quick-icon"><i data-lucide="files" aria-hidden="true"></i></span><span><strong>Documents & certificates</strong><small>Your essential barangay services</small></span><i class="quick-arrow" data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                    <a href="#announcements"><span class="quick-icon gold"><i data-lucide="megaphone" aria-hidden="true"></i></span><span><strong>Community noticeboard</strong><small>Stay in the know, wherever you are</small></span><i class="quick-arrow" data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                    <a href="#about"><span class="quick-icon blue"><i data-lucide="building-2" aria-hidden="true"></i></span><span><strong>Get to know your barangay</strong><small>Local service. Shared purpose.</small></span><i class="quick-arrow" data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                </div>
            </div>
            <section id="services" class="services-section section-space" aria-labelledby="services-heading">
                <div class="container">
                    <div class="section-heading"><div><div class="eyebrow">HERE TO HELP</div><h2 id="services-heading">What can we help you with?</h2><p>Find the barangay service you need, all in one place.</p></div><span class="section-note"><i data-lucide="hand-heart" aria-hidden="true"></i> People first. Always.</span></div>
                    <div class="service-grid">
                        @foreach ([
                            ['icon' => 'file-check-2', 'tone' => 'green', 'category' => 'DOCUMENTS', 'title' => 'Barangay Clearance', 'description' => 'Local certification for employment, business, and other personal requirements.', 'detail' => 'Contact the barangay office to confirm the documents, fees, and processing time for your clearance. Online applications are not yet available.'],
                            ['icon' => 'house', 'tone' => 'blue', 'category' => 'CERTIFICATES', 'title' => 'Certificate of Residency', 'description' => 'Proof of your residence for applications and official transactions.', 'detail' => 'The barangay office can help verify your residency and explain the certification process. Online applications are not yet available.'],
                            ['icon' => 'heart-handshake', 'tone' => 'gold', 'category' => 'ASSISTANCE', 'title' => 'Certificate of Indigency', 'description' => 'Supporting documentation for financial, medical, or educational assistance.', 'detail' => 'Contact the barangay office for eligibility assessment and the current requirements for this certificate. Online applications are not yet available.'],
                            ['icon' => 'notebook-pen', 'tone' => 'rose', 'category' => 'COMMUNITY SUPPORT', 'title' => 'Blotter & Concerns', 'description' => 'Find support for community concerns and the recording of incidents.', 'detail' => 'Visit the barangay office to discuss a concern or ask about recording an incident. This page does not submit or monitor reports.'],
                        ] as $service)
                            <button type="button" class="service-card" data-detail-title="{{ $service['title'] }}" data-detail-text="{{ $service['detail'] }}" data-detail-label="Resident service">
                                <span class="service-icon {{ $service['tone'] }}"><i data-lucide="{{ $service['icon'] }}" aria-hidden="true"></i></span>
                                <span class="service-category">{{ $service['category'] }}</span>
                                <h3>{{ $service['title'] }}</h3>
                                <p>{{ $service['description'] }}</p>
                                <span class="service-link">View details <i data-lucide="arrow-right" aria-hidden="true"></i></span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </section>
            <section id="announcements" class="announcements-section section-space" aria-labelledby="announcements-heading">
                <div class="container news-layout">
                    <div class="noticeboard">
                        <div class="section-heading"><div><div class="eyebrow">AROUND THE BARANGAY</div><h2 id="announcements-heading">Community noticeboard</h2><p>Good things happen when we stay connected.</p></div><span class="sample-label">Sample notices</span></div>
                        <div class="notice-filters" role="group" aria-label="Filter community notices">
                            <button type="button" class="filter active" data-filter="all" aria-pressed="true">All updates</button>
                            <button type="button" class="filter" data-filter="advisory" aria-pressed="false">Advisories</button>
                            <button type="button" class="filter" data-filter="event" aria-pressed="false">Events</button>
                            <button type="button" class="filter" data-filter="program" aria-pressed="false">Programs</button>
                        </div>
                        <div class="notice-list" aria-live="polite">
                            @foreach ([
                                ['type' => 'advisory', 'label' => 'Advisory', 'icon' => 'clipboard-list', 'title' => 'Preparing for your next barangay visit', 'description' => 'A little preparation makes every transaction easier.', 'detail' => 'Before visiting, contact the barangay office to confirm office hours and the requirements for your transaction. This is sample homepage content, not an official advisory.'],
                                ['type' => 'event', 'label' => 'Community event', 'icon' => 'sprout', 'title' => 'Together for a cleaner community', 'description' => 'Small acts of care make a big difference where we live.', 'detail' => 'A space for upcoming community cleanups and volunteer activities. Dates and meeting locations will be published when confirmed. This is a sample notice.'],
                                ['type' => 'program', 'label' => 'Community program', 'icon' => 'heart-pulse', 'title' => 'Making community wellness a priority', 'description' => 'Health information and local support for every household.', 'detail' => 'A space for confirmed health programs and community wellness updates. Contact the barangay office for currently available services. This is a sample notice.'],
                            ] as $notice)
                                <button type="button" class="notice-item" data-notice-type="{{ $notice['type'] }}" data-detail-title="{{ $notice['title'] }}" data-detail-text="{{ $notice['detail'] }}" data-detail-label="{{ $notice['label'] }}">
                                    <span class="notice-icon {{ $notice['type'] }}"><i data-lucide="{{ $notice['icon'] }}" aria-hidden="true"></i></span>
                                    <span class="notice-copy"><span class="notice-category {{ $notice['type'] }}">{{ $notice['label'] }}</span><h3>{{ $notice['title'] }}</h3><span class="notice-description">{{ $notice['description'] }}</span></span>
                                    <i class="notice-arrow" data-lucide="arrow-up-right" aria-hidden="true"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <aside class="community-aside" aria-labelledby="community-heading">
                        <span class="aside-icon"><i data-lucide="messages-square" aria-hidden="true"></i></span>
                        <div class="eyebrow">LET'S TALK</div>
                        <h2 id="community-heading">Your voice.<br>Our community.</h2>
                        <p>Questions, ideas, or a concern to share? Your barangay is here to listen.</p>
                        <a href="#about" class="button button-white">Connect with your barangay <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                        <div class="aside-divider"></div>
                        <span class="aside-bottom"><i data-lucide="users-round" aria-hidden="true"></i> Better communities begin with a conversation.</span>
                    </aside>
                </div>
            </section>
            <section id="about" class="about-section" aria-labelledby="about-heading">
                <div class="container about-inner">
                    <span class="about-mark"><i data-lucide="landmark" aria-hidden="true"></i></span>
                    <div class="about-copy"><div class="eyebrow">AT THE HEART OF LOCAL SERVICE</div><h2 id="about-heading">A stronger barangay starts with us.</h2><p>Connecting residents with local information and everyday services.<br>For office hours, requirements, and assistance, contact your barangay office.</p></div>
                    <a class="text-link" href="#services">Find a service <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                </div>
            </section>
        </main>
        <footer class="site-footer">
            <div class="container footer-inner"><a href="{{ route('home') }}" class="footer-brand"><i data-lucide="landmark" aria-hidden="true"></i> Barangay Information System</a><span>&copy; {{ date('Y') }} Barangay Information System</span><a href="#main">Back to top <i data-lucide="arrow-up" aria-hidden="true"></i></a></div>
        </footer>
        <dialog id="detail-dialog" aria-labelledby="detail-title" aria-describedby="detail-text">
            <div class="dialog-top"><span class="eyebrow" id="detail-label"></span><button type="button" class="icon-button dialog-close" aria-label="Close details" title="Close details"><i data-lucide="x" aria-hidden="true"></i></button></div>
            <h2 id="detail-title"></h2><p id="detail-text"></p><button type="button" class="button button-primary dialog-close">Close <i data-lucide="check" aria-hidden="true"></i></button>
        </dialog>
    </body>
</html>
