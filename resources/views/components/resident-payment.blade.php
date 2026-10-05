@props(['serviceRequest', 'onlinePaymentAvailable'])

<section class="resident-card payment-card" aria-labelledby="payment-title">
    <div class="resident-detail-heading">
        <span class="resident-section-icon"><i data-lucide="landmark" aria-hidden="true"></i></span>
        <div><h2 id="payment-title">Payment</h2><p>Pay through QR Ph or at the barangay hall.</p></div>
    </div>
    @error('payment')
        <div class="resident-alert resident-alert-warning" role="alert"><i data-lucide="info" aria-hidden="true"></i><span>{{ $message }}</span></div>
    @enderror

    @if ($serviceRequest->latestPayment?->provider === \App\Models\Payment::PROVIDER_PAYMONGO_QRPH && $serviceRequest->latestPayment->status === \App\Models\Payment::STATUS_PENDING)
        <div class="payment-active" data-payment-monitor data-status-url="{{ route('account.payments.status', $serviceRequest->latestPayment) }}">
            <span class="payment-checkout-icon"><i data-lucide="qr-code" aria-hidden="true"></i></span>
            <div class="payment-copy">
                <span class="payment-label"><i data-lucide="shield-check" aria-hidden="true"></i> QR PH PAYMENT</span>
                <strong>₱{{ number_format((float) $serviceRequest->latestPayment->amount, 2) }}</strong>
                <p>Open PayMongo checkout to view your payment QR. Scan it with GCash, Maya, or a compatible banking app. Your payment status updates automatically after confirmation.</p>
                <span class="payment-live-status" data-payment-status role="status" aria-live="polite">Waiting for payment confirmation…</span>
                <small>Checkout available until {{ $serviceRequest->latestPayment->expires_at?->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}.</small>
                <form method="POST" action="{{ route('account.requests.payment.store', $serviceRequest) }}">
                    @csrf
                    <button type="submit" class="resident-button resident-button-primary" @disabled(! $onlinePaymentAvailable)><i data-lucide="external-link" aria-hidden="true"></i> Continue to checkout</button>
                </form>
            </div>
        </div>
        <form method="POST" action="{{ route('account.requests.cash-payment.store', $serviceRequest) }}" class="payment-method-switch">
            @csrf
            <button type="submit" class="resident-button resident-button-outline"><i data-lucide="landmark" aria-hidden="true"></i> Pay cash instead</button>
        </form>
    @elseif ($serviceRequest->latestPayment?->provider === \App\Models\Payment::PROVIDER_CASH && $serviceRequest->latestPayment->status === \App\Models\Payment::STATUS_PENDING)
        <div class="cash-payment-card" data-payment-monitor data-status-url="{{ route('account.payments.status', $serviceRequest->latestPayment) }}">
            <span class="cash-payment-icon"><i data-lucide="landmark" aria-hidden="true"></i></span>
            <div>
                <span class="payment-label"><i data-lucide="check" aria-hidden="true"></i> CASH PAYMENT</span>
                <strong>₱{{ number_format((float) $serviceRequest->latestPayment->amount, 2) }}</strong>
                <p>Pay at the barangay hall and present request <b>#{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</b>. Staff will record the receipt and confirm your payment.</p>
                <span class="payment-live-status" data-payment-status role="status" aria-live="polite">Waiting for staff confirmation…</span>
            </div>
        </div>
        <form method="POST" action="{{ route('account.requests.payment.store', $serviceRequest) }}" class="payment-method-switch">
            @csrf
            <button type="submit" class="resident-button resident-button-outline" @disabled(! $onlinePaymentAvailable)><i data-lucide="qr-code" aria-hidden="true"></i> Switch to QR Ph</button>
        </form>
    @else
        @if ($serviceRequest->latestPayment?->status === \App\Models\Payment::STATUS_EXPIRED)
            <div class="payment-expired"><i data-lucide="clock" aria-hidden="true"></i><span>The previous checkout expired. Start a new payment or choose cash at the barangay hall.</span></div>
        @endif
        <div class="payment-method-intro"><strong>Amount due: ₱{{ number_format((float) $serviceRequest->fee_amount, 2) }}</strong><p>Choose how you want to pay.</p></div>
        <div class="payment-method-choices">
            <form method="POST" action="{{ route('account.requests.payment.store', $serviceRequest) }}" class="payment-method-choice">
                @csrf
                <button type="submit" @disabled(! $onlinePaymentAvailable)><span><i data-lucide="qr-code" aria-hidden="true"></i></span><strong>Pay with QR Ph</strong><small>Scan with GCash, Maya, or your banking app. Confirmation is automatic.</small></button>
            </form>
            <form method="POST" action="{{ route('account.requests.cash-payment.store', $serviceRequest) }}" class="payment-method-choice">
                @csrf
                <button type="submit"><span><i data-lucide="landmark" aria-hidden="true"></i></span><strong>Cash at Barangay Hall</strong><small>Pay in person and let staff record your receipt.</small></button>
            </form>
        </div>
    @endif
    @if (! $onlinePaymentAvailable)
        <p class="payment-unavailable"><i data-lucide="info" aria-hidden="true"></i> Online payment is not available yet. You can pay cash at the barangay hall.</p>
    @endif
</section>
