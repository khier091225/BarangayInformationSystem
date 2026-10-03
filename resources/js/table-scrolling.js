export default function initializeTableScrolling() {
    document.querySelectorAll('[data-table-scroll-panel]').forEach((panel, index) => {
        const scrollRegion = panel.querySelector('[data-table-scroll]');
        const hint = panel.querySelector('[data-table-scroll-hint]');
        const table = scrollRegion?.querySelector('table');
        if (!scrollRegion || !hint || !table) return;

        hint.id = `table-scroll-hint-${index + 1}`;
        let resizeFrame = null;

        function updateHint() {
            const hasOverflow = scrollRegion.scrollWidth > scrollRegion.clientWidth + 1;
            hint.hidden = !hasOverflow;
            scrollRegion.tabIndex = hasOverflow ? 0 : -1;

            if (hasOverflow) {
                scrollRegion.setAttribute('aria-describedby', hint.id);
            } else {
                scrollRegion.removeAttribute('aria-describedby');
            }
        }

        function scheduleUpdate() {
            if (resizeFrame !== null) return;
            resizeFrame = requestAnimationFrame(() => {
                resizeFrame = null;
                updateHint();
            });
        }

        updateHint();

        if ('ResizeObserver' in window) {
            const observer = new ResizeObserver(scheduleUpdate);
            observer.observe(scrollRegion);
            observer.observe(table);
        } else {
            window.addEventListener('resize', scheduleUpdate);
        }
    });
}
