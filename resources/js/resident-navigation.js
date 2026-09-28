export default function initializeResidentNavigation() {
    const sidebar = document.querySelector('#resident-navigation');
    const toggle = document.querySelector('[data-resident-sidebar-toggle]');
    const closeButton = document.querySelector('[data-resident-sidebar-close]');
    const backdrop = document.querySelector('[data-resident-sidebar-backdrop]');
    const shell = document.querySelector('.resident-app-shell');
    const chatbot = document.querySelector('.project-chat');

    if (!sidebar || !toggle || !backdrop || !shell) return;

    const desktop = window.matchMedia('(min-width: 1100px)');

    function setOpen(open, restoreFocus = false) {
        const shouldOpen = open && !desktop.matches;
        sidebar.classList.toggle('open', shouldOpen);
        backdrop.hidden = !shouldOpen;
        shell.inert = shouldOpen;
        if (chatbot) chatbot.inert = shouldOpen;
        document.body.classList.toggle('resident-navigation-open', shouldOpen);
        toggle.setAttribute('aria-expanded', String(shouldOpen));
        toggle.setAttribute('aria-label', shouldOpen ? 'Close resident navigation' : 'Open resident navigation');

        if (shouldOpen) {
            sidebar.querySelector('a')?.focus({ preventScroll: true });
        } else if (restoreFocus && !desktop.matches) {
            toggle.focus({ preventScroll: true });
        }
    }

    toggle.addEventListener('click', () => setOpen(!sidebar.classList.contains('open')));
    closeButton?.addEventListener('click', () => setOpen(false, true));
    backdrop.addEventListener('click', () => setOpen(false, true));

    sidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => setOpen(false));
    });

    document.addEventListener('keydown', event => {
        if (!sidebar.classList.contains('open')) return;

        if (event.key === 'Escape') {
            setOpen(false, true);
        }

        if (event.key === 'Tab') {
            const focusable = [...sidebar.querySelectorAll('a, button')].filter(element => element.offsetParent !== null);
            const first = focusable[0];
            const last = focusable.at(-1);

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    desktop.addEventListener('change', () => {
        const focusWasInSidebar = sidebar.contains(document.activeElement);
        setOpen(false);
        if (!desktop.matches && focusWasInSidebar) toggle.focus({ preventScroll: true });
    });
}
