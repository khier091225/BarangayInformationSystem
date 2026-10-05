export default function initializePayments() {
    document.querySelectorAll('[data-payment-monitor]').forEach(monitor => {
        const statusText = monitor.querySelector('[data-payment-status]');
        const waitingMessage = statusText.textContent;
        let pollTimer;
        let stopped = false;

        const poll = async () => {
            if (stopped) return;
            try {
                const response = await fetch(monitor.dataset.statusUrl, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                    signal: AbortSignal.timeout(20000),
                });
                if (stopped) return;
                if (response.status === 401 || response.status === 403 || response.redirected) {
                    statusText.textContent = 'Please sign in again to check your payment. Your payment confirmation is saved separately.';
                    return;
                }
                if (!response.ok) throw new Error('Status request failed');

                const payment = await response.json();
                if (stopped) return;
                if (payment.status === 'paid' && payment.redirect_url) {
                    statusText.textContent = 'Payment confirmed. Updating your request…';
                    monitor.classList.add('payment-detected');
                    window.location.replace(payment.redirect_url);
                    return;
                }
                if (payment.status === 'expired' || payment.status === 'cancelled') {
                    statusText.textContent = payment.status === 'expired'
                        ? 'This checkout has expired. Refresh the page to choose a payment method.'
                        : 'This payment method was changed. Refresh the page to view your request.';
                    monitor.classList.add('payment-expired-state');
                    return;
                }
                if (payment.checking_available === false) {
                    statusText.textContent = 'Confirmation is taking longer than usual. We will keep checking automatically.';
                } else {
                    statusText.textContent = waitingMessage;
                }
            } catch {
                if (!stopped) statusText.textContent = 'Unable to check right now. We will retry automatically.';
            }
            if (!stopped) pollTimer = window.setTimeout(poll, 5000);
        };

        poll();
        window.addEventListener('pagehide', () => {
            stopped = true;
            window.clearTimeout(pollTimer);
        }, { once: true });
    });
}
