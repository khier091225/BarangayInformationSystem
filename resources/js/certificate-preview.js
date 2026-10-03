export default function initializeCertificatePreview() {
    const preview = document.querySelector('[data-certificate-preview]');
    if (!preview) return;

    const scrollRegion = preview.querySelector('.certificate-preview-scroll');
    const sheet = preview.querySelector('.certificate-sheet');
    const tools = preview.querySelector('[data-certificate-preview-tools]');
    const scaleOutput = preview.querySelector('[data-certificate-preview-scale]');
    const modeButtons = [...preview.querySelectorAll('[data-certificate-preview-mode]')];
    let mode = 'fit';
    let resizeFrame = null;

    function updatePreview() {
        const sheetStyle = getComputedStyle(sheet);
        const width = parseFloat(sheetStyle.width);
        const height = parseFloat(sheetStyle.height);
        const availableWidth = scrollRegion.clientWidth;
        if (!width || !height || !availableWidth) return;

        const scale = mode === 'fit' ? Math.min(1, availableWidth / width) : 1;
        preview.style.setProperty('--certificate-preview-scale', String(scale));
        preview.style.setProperty('--certificate-preview-width', `${width * scale}px`);
        preview.style.setProperty('--certificate-preview-height', `${height * scale}px`);

        const percentage = `${Math.round(scale * 100)}%`;
        if (scaleOutput.textContent !== percentage) scaleOutput.textContent = percentage;

        modeButtons.forEach(button => {
            const active = button.dataset.certificatePreviewMode === mode;
            button.setAttribute('aria-pressed', String(active));
            button.classList.toggle('button-primary', active);
            button.classList.toggle('button-outline', !active);
        });
    }

    function scheduleUpdate() {
        if (resizeFrame !== null) return;
        resizeFrame = requestAnimationFrame(() => {
            resizeFrame = null;
            updatePreview();
        });
    }

    modeButtons.forEach(button => {
        button.addEventListener('click', () => {
            mode = button.dataset.certificatePreviewMode;
            updatePreview();
            if (mode === 'fit') scrollRegion.scrollLeft = 0;
        });
    });

    updatePreview();
    tools.hidden = false;

    if ('ResizeObserver' in window) {
        const observer = new ResizeObserver(scheduleUpdate);
        observer.observe(scrollRegion);
        observer.observe(sheet);
    } else {
        window.addEventListener('resize', scheduleUpdate);
    }
}
