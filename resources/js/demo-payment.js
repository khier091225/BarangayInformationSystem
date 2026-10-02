import QRCode from 'qrcode';

export default function initializeDemoPayments() {
    document.querySelectorAll('[data-demo-payment-qr]').forEach(canvas => {
        QRCode.toCanvas(canvas, canvas.dataset.qrValue, {
            width: 232,
            margin: 1,
            color: { dark: '#123d2d', light: '#ffffff' },
            errorCorrectionLevel: 'M',
        }).catch(() => {
            canvas.replaceWith(Object.assign(document.createElement('p'), {
                className: 'demo-payment-qr-error',
                textContent: 'The QR code could not be displayed. Use the link beside it instead.',
            }));
        });
    });

    document.querySelectorAll('[data-demo-payment-monitor]').forEach(monitor => {
        const statusText = monitor.querySelector('[data-demo-payment-status]');
        let pollTimer;

        const stopPolling = () => window.clearTimeout(pollTimer);
        const poll = async () => {
            try {
                const response = await fetch(monitor.dataset.statusUrl, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });

                if (!response.ok) throw new Error('Status request failed');

                const payment = await response.json();
                if (payment.status === 'paid' && payment.redirect_url) {
                    statusText.textContent = 'Payment detected. Updating your request…';
                    monitor.classList.add('demo-payment-detected');
                    window.setTimeout(() => window.location.replace(payment.redirect_url), 650);
                    return;
                }

                if (payment.status === 'expired') {
                    statusText.textContent = 'This QR code has expired. Refresh the page to generate a new one.';
                    monitor.classList.add('demo-payment-expired-state');
                    return;
                }
            } catch {
                statusText.textContent = 'Checking payment status…';
            }

            pollTimer = window.setTimeout(poll, 2500);
        };

        poll();
        window.addEventListener('pagehide', stopPolling, { once: true });
    });
}
