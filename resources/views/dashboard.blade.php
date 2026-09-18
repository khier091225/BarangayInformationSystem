<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#11665e">
        <title>Overview | Barangay Information System</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="workspace-page">
        <a class="skip-link" href="#workspace-main">Skip to content</a>
        <button class="sidebar-backdrop" aria-label="Close navigation" type="button" hidden></button>
        <aside class="workspace-sidebar" id="workspace-navigation" aria-label="Workspace navigation">
            <a href="{{ route('home') }}" class="brand workspace-brand"><span class="brand-mark"><i data-lucide="landmark" aria-hidden="true"></i></span><span class="brand-name">Barangay<span>Information System</span></span></a>
            <div class="workspace-label">Staff workspace <span class="sample-label">Demo</span></div>
            <nav class="workspace-nav" aria-label="Main workspace">
                <a href="#overview" data-section="overview" class="selected" aria-current="page"><i data-lucide="layout-dashboard" aria-hidden="true"></i> Overview</a>
                <span class="nav-group-label">Records</span>
                <a href="#residents" data-section="residents"><i data-lucide="users-round" aria-hidden="true"></i> Residents</a>
                <a href="#households" data-section="households"><i data-lucide="house" aria-hidden="true"></i> Households</a>
                <a href="#certificates" data-section="certificates"><i data-lucide="files" aria-hidden="true"></i> Certificates <span class="nav-count" id="pending-count" aria-label="6 pending requests">6</span></a>
                <a href="#blotter" data-section="blotter"><i data-lucide="notebook-pen" aria-hidden="true"></i> Blotter records</a>
                <a href="#officials" data-section="officials"><i data-lucide="badge-check" aria-hidden="true"></i> Officials</a>
                <span class="nav-group-label">Community</span>
                <a href="#reports" data-section="reports"><i data-lucide="chart-no-axes-combined" aria-hidden="true"></i> Reports</a>
            </nav>
            <div class="sidebar-bottom">
                <a class="public-site-link" href="{{ route('home') }}"><i data-lucide="globe" aria-hidden="true"></i> Public website <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                <div class="sidebar-profile"><span class="staff-avatar" aria-hidden="true">BS</span><span><strong>Barangay staff</strong><small>Demo session</small></span><form method="POST" action="{{ route('logout') }}">@csrf<button class="signout-button" title="Sign out" aria-label="Sign out" type="submit"><i data-lucide="log-out" aria-hidden="true"></i></button></form></div>
            </div>
        </aside>
        <div class="workspace-shell">
            <header class="workspace-topbar">
                <div class="workspace-breadcrumb"><button type="button" class="icon-button sidebar-toggle" aria-label="Open sidebar" aria-controls="workspace-navigation" aria-expanded="false"><i data-lucide="panel-left" aria-hidden="true"></i></button><span>Workspace</span><i data-lucide="chevron-right" aria-hidden="true"></i><strong id="breadcrumb-current">Overview</strong></div>
                <div class="topbar-actions"><span class="demo-tag">Sample data</span><button type="button" class="notification-button" aria-label="View notifications" title="Sample notifications" data-workspace-detail="notifications"><i data-lucide="bell" aria-hidden="true"></i></button></div>
            </header>
            <main class="workspace-main" id="workspace-main" tabindex="-1">
                <div class="workspace-heading">
                    <div><h1 id="workspace-title" tabindex="-1">Overview</h1><p id="workspace-subtitle">Explore sample records and everyday barangay workflows.</p></div>
                    <button type="button" class="button button-primary" data-new-resident><i data-lucide="plus" aria-hidden="true"></i> Add demo resident</button>
                </div>
                <div class="demo-notice"><i data-lucide="info" aria-hidden="true"></i><p>This is a demo, not a live records system. Added residents last until you reload.</p></div>

                <div id="overview-view">
                    <div class="workflow-links" aria-label="Workspace shortcuts">
                        <a href="#certificates"><i data-lucide="files" aria-hidden="true"></i><span><strong>Certificate requests</strong><small><span id="pending-summary">6 pending</span> in the sample directory</small></span><i data-lucide="arrow-right" aria-hidden="true"></i></a>
                        <a href="#residents"><i data-lucide="users-round" aria-hidden="true"></i><span><strong>Find a resident</strong><small>Search names, IDs, and puroks</small></span><i data-lucide="arrow-right" aria-hidden="true"></i></a>
                        <a href="#blotter"><i data-lucide="notebook-pen" aria-hidden="true"></i><span><strong>Blotter records</strong><small>View sample cases and hearings</small></span><i data-lucide="arrow-right" aria-hidden="true"></i></a>
                    </div>
                    <section class="requests-section workspace-panel" aria-labelledby="requests-heading">
                        <div class="workspace-section-heading"><div><h2 id="requests-heading">Recent certificate requests</h2><p>The five most recent sample requests.</p></div><a class="workspace-text-link" href="#certificates">View all requests <i data-lucide="arrow-right" aria-hidden="true"></i></a></div>
                        <div class="request-toolbar">
                            <label class="search-field"><span>Search recent requests</span><span class="workspace-search"><i data-lucide="search" aria-hidden="true"></i><input type="search" id="request-search" placeholder="Name or reference"></span></label>
                            <label class="filter-field"><span>Status</span><select id="request-status"><option value="all">All statuses</option><option>Pending</option><option>Issued</option><option>Processing</option></select></label>
                            <button class="button button-outline" type="button" id="reset-requests">Reset</button>
                        </div>
                        <div class="table-scroll" role="region" tabindex="0" aria-label="Recent certificate requests table"><table class="workspace-table"><thead><tr><th scope="col">Resident</th><th scope="col">Document</th><th scope="col">Status</th><th scope="col">Date</th><th scope="col"><span class="sr-only">Details</span></th></tr></thead><tbody id="recent-requests"></tbody></table></div>
                        <div class="table-footer"><span id="request-summary" role="status" aria-live="polite"></span><span>Sample transactions · September 2026</span></div>
                    </section>

                    <section class="snapshot-section" aria-labelledby="snapshot-heading">
                        <div class="workspace-section-heading"><div><h2 id="snapshot-heading">Illustrative community snapshot</h2><p>September 2026 example figures. Independent of the sample directories.</p></div><a class="workspace-text-link" href="#reports">View report <i data-lucide="arrow-right" aria-hidden="true"></i></a></div>
                        <dl class="workspace-stats">
                            <div><dt>Residents</dt><dd id="resident-count">1,248</dd><span>Illustrative population</span></div>
                            <div><dt>Households</dt><dd>326</dd><span>Across six puroks</span></div>
                            <div><dt>Certificates issued</dt><dd>48</dd><span>September 2026</span></div>
                            <div><dt>Open blotter cases</dt><dd>4</dd><span>Illustrative case count</span></div>
                        </dl>
                        <div class="workspace-analytics">
                            <section class="activity-chart workspace-panel" aria-labelledby="chart-heading">
                                <div class="workspace-section-heading"><div><h3 id="chart-heading">Certificate activity</h3><p>Illustrative documents issued</p></div><label class="filter-field"><span>Chart period</span><select id="chart-period"><option value="recent">Apr – Sep 2026</option><option value="previous">Oct 2025 – Mar 2026</option></select></label></div>
                                <div class="chart-summary"><strong id="chart-total">242</strong><span>certificates</span><span class="chart-trend" id="chart-trend"></span></div>
                                <div class="bar-chart" id="certificate-chart" aria-hidden="true"><div class="chart-axis"><span>60</span><span>40</span><span>20</span><span>0</span></div><div class="chart-plot" id="chart-bars"></div></div>
                                <details class="chart-data"><summary>View chart data</summary><table class="workspace-table"><caption class="sr-only">Illustrative monthly certificates issued</caption><thead><tr><th scope="col">Month</th><th scope="col">Certificates</th></tr></thead><tbody id="chart-data"></tbody></table></details>
                            </section>
                            <section class="population-section workspace-panel" aria-labelledby="population-heading"><h3 id="population-heading">Resident demographics</h3><p class="section-description">Illustrative population of 1,248 residents</p><dl class="population-legend"><div><dt>Female</dt><dd>652 <span>52.2%</span></dd></div><div><dt>Male</dt><dd>596 <span>47.8%</span></dd></div></dl><p class="report-note">These figures describe the example snapshot, not the sample directory or temporary additions.</p><a class="workspace-text-link" href="#reports">Population by purok <i data-lucide="arrow-right" aria-hidden="true"></i></a></section>
                        </div>
                    </section>
                    <section class="activity-section" aria-labelledby="activity-heading"><div class="workspace-section-heading"><div><h2 id="activity-heading">Sample activity</h2><p>Illustrative events · September 18, 2026</p></div></div><ol class="activity-timeline"><li><i data-lucide="file-check-2" aria-hidden="true"></i><div><strong>Certificate issued</strong><p>Barangay clearance for Maria Santos</p><small>10:42 AM · Certificates</small></div></li><li><i data-lucide="user-round-plus" aria-hidden="true"></i><div><strong>Resident registered</strong><p>Carlo Reyes added to Purok 3</p><small>10:15 AM · Residents</small></div></li><li><i data-lucide="calendar-clock" aria-hidden="true"></i><div><strong>Hearing scheduled</strong><p>Case BL-2026-014 · Sep 21, 9:00 AM</p><a class="workspace-text-link" href="#blotter">View sample cases</a></div></li></ol></section>
                </div>

                <section id="records-view" class="workspace-panel" hidden aria-labelledby="records-heading">
                    <div class="records-heading-row"><div><h2 id="records-heading">Resident directory</h2><p id="records-description">Browse sample records.</p></div><button type="button" class="button button-outline" id="export-records"><i data-lucide="download" aria-hidden="true"></i> Export results</button></div>
                    <div class="request-toolbar">
                        <label class="search-field"><span id="records-search-label">Search records</span><span class="workspace-search"><i data-lucide="search" aria-hidden="true"></i><input type="search" id="records-search" placeholder="Name, reference, or keyword" aria-labelledby="records-search-label"></span></label>
                        <label class="filter-field" id="records-filter-field"><span id="records-filter-label">Purok</span><select id="records-filter" aria-labelledby="records-filter-label"></select></label>
                        <button type="button" class="button button-outline" id="reset-records">Reset</button>
                    </div>
                    <p class="record-count" id="records-count" role="status" aria-live="polite"></p>
                    <div class="table-scroll" role="region" tabindex="0" aria-labelledby="records-heading"><table class="workspace-table"><thead id="records-head"></thead><tbody id="records-body"></tbody></table></div>
                    <div class="records-empty" id="records-empty" hidden><i data-lucide="search-x" aria-hidden="true"></i><h3>No matching records</h3><p>Try a different keyword or reset the filters above.</p></div>
                    <div class="table-footer"><span>Sample records only · No live barangay data</span><span>CSV includes the current search and filters.</span></div>
                </section>
                <section id="reports-view" class="workspace-panel" hidden aria-labelledby="reports-heading">
                    <div class="records-heading-row"><div><h2 id="reports-heading">Illustrative community snapshot</h2><p>September 2026 · Example figures, not live directory totals.</p></div><button type="button" class="button button-outline" id="export-report"><i data-lucide="download" aria-hidden="true"></i> Export purok summary</button></div>
                    <dl class="report-metrics"><div><dt>Residents</dt><dd>1,248</dd></div><div><dt>Households</dt><dd>326</dd></div><div><dt>Average household size</dt><dd>3.8</dd></div></dl>
                    <h3 class="report-subheading">Resident distribution by purok</h3><div id="purok-report"></div><p class="report-note">Illustrative totals are independent of the sample resident directory. Temporary additions do not change this snapshot. The export contains these six purok totals.</p>
                </section>
                <footer class="workspace-footer"><span>&copy; {{ date('Y') }} Barangay Information System</span><span>Demo workspace · No live records</span></footer>
            </main>
        </div>
        <dialog id="workspace-dialog" aria-labelledby="workspace-dialog-title"><div class="dialog-top"><button type="button" class="icon-button" data-close-workspace-dialog aria-label="Close dialog"><i data-lucide="x" aria-hidden="true"></i></button></div><h2 id="workspace-dialog-title"></h2><div id="workspace-dialog-content"></div><button class="button button-primary" type="button" data-close-workspace-dialog>Done <i data-lucide="check" aria-hidden="true"></i></button></dialog>
        <dialog id="resident-dialog" aria-labelledby="resident-dialog-title" aria-describedby="resident-form-note">
            <div class="dialog-top"><button type="button" class="icon-button" data-close-resident-dialog aria-label="Close resident form"><i data-lucide="x" aria-hidden="true"></i></button></div>
            <h2 id="resident-dialog-title">Add a demo resident</h2><p class="form-note" id="resident-form-note">All fields are required. This entry resets on reload and does not change the illustrative community snapshot.</p>
            <form id="resident-form" novalidate>
                <div class="form-grid">
                    <label for="resident-first-name">First name<input id="resident-first-name" name="firstName" autocomplete="given-name" maxlength="60" required aria-describedby="first-name-error"><span class="field-error" id="first-name-error" hidden></span></label>
                    <label for="resident-last-name">Last name<input id="resident-last-name" name="lastName" autocomplete="family-name" maxlength="60" required aria-describedby="last-name-error"><span class="field-error" id="last-name-error" hidden></span></label>
                    <label for="resident-purok">Purok<select id="resident-purok" name="purok" required aria-describedby="purok-error"><option value="">Select purok</option>@for ($purok = 1; $purok <= 6; $purok++)<option>Purok {{ $purok }}</option>@endfor</select><span class="field-error" id="purok-error" hidden></span></label>
                    <label for="resident-gender">Gender<select id="resident-gender" name="gender" required aria-describedby="gender-error"><option value="">Select gender</option><option>Female</option><option>Male</option><option>Prefer not to say</option></select><span class="field-error" id="gender-error" hidden></span></label>
                </div>
                <div class="form-actions"><button class="button button-outline" type="button" data-close-resident-dialog>Cancel</button><button class="button button-primary" type="submit"><i data-lucide="plus" aria-hidden="true"></i> Add demo resident</button></div>
            </form>
        </dialog>
        <div class="workspace-toast" id="workspace-toast" role="status" aria-live="polite" hidden></div>
    </body>
</html>
