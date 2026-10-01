const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

export function closeDialog(dialog) {
    if (!dialog?.open || dialog.hasAttribute('data-motion-closing')) return;

    if (reducedMotion.matches) {
        dialog.close();
        return;
    }

    dialog.setAttribute('data-motion-closing', '');

    function finish() {
        window.clearTimeout(timeout);
        dialog.removeEventListener('animationend', onAnimationEnd);
        if (dialog.open) dialog.close();
        dialog.removeAttribute('data-motion-closing');
    }

    function onAnimationEnd(event) {
        if (event.target === dialog && event.animationName === 'bis-dialog-out') finish();
    }

    dialog.addEventListener('animationend', onAnimationEnd);
    const timeout = window.setTimeout(finish, 220);
}

export function initializeDialogMotion() {
    document.addEventListener('click', event => {
        const closeButton = event.target.closest('[data-household-dialog-close], [data-record-dialog-close], [data-service-dialog-close], .dialog-close');
        if (!closeButton) return;

        event.preventDefault();
        closeDialog(closeButton.closest('dialog'));
    });

    document.addEventListener('cancel', event => {
        if (!(event.target instanceof HTMLDialogElement)) return;

        event.preventDefault();
        closeDialog(event.target);
    }, true);
}

export function initializeFormFeedback() {
    document.querySelectorAll('form[method="POST"], form[method="post"]').forEach(form => {
        if (form.matches('[data-registration-code-form], [data-demo-entry]')) return;

        let submitting = false;
        let activeSubmit = null;
        let originalLabel = null;
        let submitText = null;
        let originalSubmitText = null;

        form.addEventListener('submit', event => {
            if (event.defaultPrevented) return;

            if (submitting) {
                event.preventDefault();
                return;
            }

            submitting = true;
            activeSubmit = event.submitter;
            form.setAttribute('data-submitting', '');
            form.setAttribute('aria-busy', 'true');

            if (activeSubmit) {
                originalLabel = activeSubmit.getAttribute('aria-label');
                submitText = activeSubmit.querySelector('[data-submit-label]');
                if (submitText && activeSubmit.dataset.loadingLabel) {
                    originalSubmitText = submitText.textContent;
                    submitText.textContent = activeSubmit.dataset.loadingLabel;
                }
                activeSubmit.setAttribute('data-active-submit', '');
                activeSubmit.setAttribute('aria-disabled', 'true');
                activeSubmit.setAttribute('aria-label', 'Submitting, please wait');
            }
        });

        window.addEventListener('pageshow', () => {
            submitting = false;
            form.removeAttribute('data-submitting');
            form.removeAttribute('aria-busy');

            if (activeSubmit) {
                if (submitText && originalSubmitText !== null) {
                    submitText.textContent = originalSubmitText;
                }
                activeSubmit.removeAttribute('data-active-submit');
                activeSubmit.removeAttribute('aria-disabled');
                if (originalLabel === null) {
                    activeSubmit.removeAttribute('aria-label');
                } else {
                    activeSubmit.setAttribute('aria-label', originalLabel);
                }
                activeSubmit = null;
                submitText = null;
                originalSubmitText = null;
            }
        });
    });
}
