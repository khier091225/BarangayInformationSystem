import { openDialog, closeDialog } from './motion';

export default function initializeRecordDialogs() {
    const initializedDialogs = new WeakSet();

    document.querySelectorAll('[data-household-dialog-trigger], [data-record-dialog-trigger]').forEach(trigger => {
        const dialog = document.getElementById(trigger.getAttribute('aria-controls'));

        if (!dialog || typeof dialog.showModal !== 'function') return;

        function openRecordDialog() {
            const invalidField = dialog.querySelector('[aria-invalid="true"]');
            const focusTarget = invalidField ?? (window.matchMedia('(min-width: 640px)').matches
                ? dialog.querySelector('input:not([type="hidden"]):not(:disabled), select:not(:disabled), textarea:not(:disabled)')
                : dialog.querySelector('[data-dialog-heading]'));

            openDialog(dialog, trigger, focusTarget);
            if (invalidField) invalidField.scrollIntoView({ block: 'center' });
        }

        trigger.addEventListener('click', event => {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

            event.preventDefault();
            openRecordDialog();
        });

        if (initializedDialogs.has(dialog)) return;
        initializedDialogs.add(dialog);

        if (dialog.hasAttribute('data-open-on-load')) openRecordDialog();
    });
}

export function initializeConfirmDialogs() {
    const dialog = document.querySelector('[data-confirm-dialog]');
    if (!dialog || typeof dialog.showModal !== 'function') return;

    const confirmButton = dialog.querySelector('[data-confirm-submit]');
    let pendingForm = null;
    let submitter = null;
    let confirmedForm = null;

    document.querySelectorAll('form[data-confirm-message]').forEach(form => {
        form.removeAttribute('onsubmit');
        form.addEventListener('submit', event => {
            if (confirmedForm === form) {
                confirmedForm = null;
                return;
            }
            event.preventDefault();
            pendingForm = form;
            submitter = event.submitter;
            dialog.querySelector('[data-dialog-heading]').textContent = form.dataset.confirmTitle;
            dialog.querySelector('[data-confirm-message]').textContent = form.dataset.confirmMessage;
            confirmButton.textContent = form.dataset.confirmLabel;
            openDialog(dialog, submitter ?? document.activeElement);
        });
    });

    confirmButton.addEventListener('click', () => {
        if (!pendingForm) return;
        const form = pendingForm;
        pendingForm = null;
        confirmedForm = form;
        if (submitter) {
            form.requestSubmit(submitter);
        } else {
            form.requestSubmit();
        }
        confirmedForm = null;
        closeDialog(dialog);
    });

    dialog.addEventListener('close', () => {
        pendingForm = null;
        submitter = null;
    });
}
