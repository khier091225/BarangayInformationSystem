@extends('layouts.auth')

@section('title', 'QRPH Payment')
@section('story-title', 'Complete your QRPH payment')
@section('story-description', 'Review the payment details and confirm the transaction to continue processing your certificate.')
@section('story-detail')
    <div class="auth-story-feature"><span><i data-lucide="shield-check" aria-hidden="true"></i></span><div><strong>Secure payment confirmation</strong><p>Check the request and amount before confirming the payment.</p></div></div>
@endsection

@section('content')
    <div class="demo-payment-page">
        <span class="demo-only-label"><i data-lucide="shield-check" aria-hidden="true"></i> QRPH PAYMENT</span>

        @if (session('success'))
            <div class="auth-status success" role="status">{{ session('success') }}</div>
        @elseif (session('warning'))
            <div class="auth-status warning" role="status">{{ session('warning') }}</div>
        @endif

        <div class="demo-payment-heading">
            <span class="demo-payment-heading-icon"><i data-lucide="qr-code" aria-hidden="true"></i></span>
            <div><p>Barangay Information System</p><h1>QRPH Payment</h1></div>
        </div>

        <dl class="demo-payment-summary">
            <div><dt>Request</dt><dd>#{{ str_pad($payment->serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</dd></div>
            <div><dt>Document</dt><dd>{{ $payment->serviceRequest->certificate_type }}</dd></div>
            <div class="demo-payment-total"><dt>Amount</dt><dd>₱{{ number_format((float) $payment->amount, 2) }}</dd></div>
        </dl>

        @if ($payment->status === \App\Models\Payment::STATUS_PENDING)
            <div class="demo-payment-notice"><i data-lucide="info" aria-hidden="true"></i><p>Review the request and payment amount before confirming.</p></div>
            <form method="POST" action="{{ $confirmUrl }}" class="demo-payment-form">
                @csrf
                <button type="submit" class="auth-submit" data-loading-label="Confirming payment…"><span data-submit-label>Confirm payment</span><i data-lucide="arrow-right" aria-hidden="true"></i></button>
            </form>
            <p class="demo-payment-expiry">This payment session expires {{ $payment->expires_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}.</p>
        @elseif ($payment->status === \App\Models\Payment::STATUS_PAID)
            <div class="demo-payment-result demo-payment-result-paid">
                <span><i data-lucide="badge-check" aria-hidden="true"></i></span>
                <h2>Payment complete</h2>
                <p>BIS has marked the request paid and issued the certificate. The original request page will update automatically.</p>
            </div>
        @else
            <div class="demo-payment-result">
                <span><i data-lucide="clock" aria-hidden="true"></i></span>
                <h2>QR code expired</h2>
                <p>Return to the resident request page and generate a new code.</p>
            </div>
        @endif
    </div>
@endsection
