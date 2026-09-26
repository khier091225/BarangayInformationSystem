export default function initializeWorkspace() {
    if (!document.body.classList.contains('workspace-page')) return;

    const select = selector => document.querySelector(selector);
    const sidebar = select('.workspace-sidebar');
    const sidebarToggle = select('.sidebar-toggle');
    const backdrop = select('.sidebar-backdrop');
    const shell = select('.workspace-shell');

    function setSidebar(open) {
        if (!sidebar) return;
        sidebar.classList.toggle('open', open);
        if (backdrop) backdrop.hidden = !open;
        document.body.classList.toggle('sidebar-open', open);
        if (sidebarToggle) sidebarToggle.setAttribute('aria-expanded', String(open));
        if (shell) shell.inert = open;
        if (open) {
            const firstLink = sidebar.querySelector('a');
            if (firstLink) firstLink.focus();
        }
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => setSidebar(true));
    }

    if (backdrop) {
        backdrop.addEventListener('click', () => {
            setSidebar(false);
            if (sidebarToggle) sidebarToggle.focus();
        });
    }

    document.addEventListener('keydown', event => {
        if (!sidebar || !sidebar.classList.contains('open')) return;
        if (event.key === 'Escape') {
            setSidebar(false);
            if (sidebarToggle) sidebarToggle.focus();
        }
        if (event.key === 'Tab') {
            const focusable = [...sidebar.querySelectorAll('a, button')];
            if (!focusable.length) return;
            const first = focusable[0];
            const last = focusable.at(-1);
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            }
            if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    let lastFocusedElement = document.activeElement;
    document.addEventListener('focusin', event => {
        lastFocusedElement = event.target;
    });

    window.matchMedia('(min-width: 960px)').addEventListener('change', event => {
        if (!sidebar) return;
        const focusWasInSidebar = sidebar.contains(lastFocusedElement);
        const focusWasOnToggle = lastFocusedElement === sidebarToggle;
        setSidebar(false);
        if (!event.matches && focusWasInSidebar && sidebarToggle) sidebarToggle.focus();
    });

    const skipLink = select('.skip-link');
    if (skipLink) {
        skipLink.addEventListener('click', event => {
            event.preventDefault();
            const main = select('#workspace-main');
            if (main) main.focus();
        });
    }

    const logoutDialog = select('#logout-confirm-dialog');
    const logoutTriggers = document.querySelectorAll('[data-logout-trigger]');
    const cancelLogoutBtn = select('#cancel-logout-btn');

    if (logoutDialog && logoutTriggers.length) {
        logoutTriggers.forEach(trigger => {
            trigger.addEventListener('click', event => {
                event.preventDefault();
                if (typeof logoutDialog.showModal === 'function') {
                    logoutDialog.showModal();
                } else {
                    window.location.href = trigger.getAttribute('href');
                }
            });
        });

        if (cancelLogoutBtn) {
            cancelLogoutBtn.addEventListener('click', () => {
                logoutDialog.close();
            });
        }

        logoutDialog.addEventListener('click', event => {
            const rect = logoutDialog.getBoundingClientRect();
            const isInDialog = (
                rect.top <= event.clientY &&
                event.clientY <= rect.top + rect.height &&
                rect.left <= event.clientX &&
                event.clientX <= rect.left + rect.width
            );
            if (!isInDialog) {
                logoutDialog.close();
            }
        });
    }
}
