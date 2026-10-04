import { closeDialog } from './motion';

export default function initializeRecordDialogs() {
    const dialogOpeners = new WeakMap();
    const initializedDialogs = new WeakSet();

    document.querySelectorAll('[data-household-dialog-trigger], [data-record-dialog-trigger]').forEach(trigger => {
        const dialog = document.getElementById(trigger.getAttribute('aria-controls'));

        if (!dialog || typeof dialog.showModal !== 'function') return;

        function openDialog() {
            dialogOpeners.set(dialog, trigger);

            if (!dialog.open) {
                dialog.showModal();
                document.body.classList.add('dialog-open');
            }

            (dialog.querySelector('[aria-invalid="true"]') ?? dialog.querySelector('input:not([type="hidden"]), select, textarea'))
                ?.focus({ preventScroll: true });
        }

        trigger.addEventListener('click', event => {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

            event.preventDefault();
            openDialog();
        });

        if (initializedDialogs.has(dialog)) return;
        initializedDialogs.add(dialog);

        dialog.addEventListener('close', () => {
            document.body.classList.remove('dialog-open');
            dialogOpeners.get(dialog)?.focus({ preventScroll: true });
            dialogOpeners.delete(dialog);
        });

        dialog.addEventListener('click', event => {
            if (event.target !== dialog) return;

            const bounds = dialog.getBoundingClientRect();
            if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) {
                closeDialog(dialog);
            }
        });

        if (dialog.hasAttribute('data-open-on-load')) openDialog();
    });
}
