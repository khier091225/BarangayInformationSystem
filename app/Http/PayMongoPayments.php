<?php

namespace App\Http;

use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Support\CertificateIssuance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PayMongoPayments
{
    public function __construct(public PayMongo $gateway, public CertificateIssuance $certificates) {}

    public function start(ServiceRequest $serviceRequest): Payment
    {
        if (! $this->gateway->isConfigured()) {
            throw ValidationException::withMessages([
                'payment' => 'Online payment is not available yet. Please contact the barangay office or choose cash payment.',
            ]);
        }

        $existing = $serviceRequest->payments()->where('provider', Payment::PROVIDER_PAYMONGO_QRPH)
            ->where('status', Payment::STATUS_PENDING)->latest('id')->first();
        if ($existing?->expires_at?->isPast()) {
            $this->synchronize($existing, force: true);
        }

        return DB::transaction(function () use ($serviceRequest): Payment {
            $lockedRequest = ServiceRequest::query()->lockForUpdate()->findOrFail($serviceRequest->id);
            abort_unless($lockedRequest->type === ServiceRequest::TYPE_CERTIFICATE
                && $lockedRequest->status === ServiceRequest::STATUS_AWAITING_PAYMENT
                && (float) $lockedRequest->fee_amount > 0, 422);

            $pending = $lockedRequest->payments()->where('status', Payment::STATUS_PENDING)->latest('id')->first();
            if ($pending?->provider === Payment::PROVIDER_PAYMONGO_QRPH) {
                abort_unless($pending->provider_livemode === $this->gateway->isLiveMode(), 422);
                if ($pending->expires_at?->isPast()) {
                    throw ValidationException::withMessages(['payment' => 'We could not close the previous checkout yet. Please try again shortly.']);
                }

                return $pending;
            }

            $payment = $lockedRequest->payments()->create([
                'provider' => Payment::PROVIDER_PAYMONGO_QRPH,
                'provider_livemode' => $this->gateway->isLiveMode(),
                'amount' => $lockedRequest->fee_amount,
                'status' => Payment::STATUS_PENDING,
                'expires_at' => now()->addMinutes(max(1, (int) config('payments.expires_minutes'))),
            ]);
            $session = $this->gateway->createCheckout($payment);
            $reference = data_get($session, 'id');
            $checkoutUrl = data_get($session, 'attributes.checkout_url');

            if (! is_string($reference) || ! preg_match('/\Acs_[a-zA-Z0-9]+\z/', $reference)
                || ! is_string($checkoutUrl) || ! $this->gateway->isCheckoutUrl($checkoutUrl)
                || data_get($session, 'attributes.livemode') !== $payment->provider_livemode) {
                throw ValidationException::withMessages(['payment' => 'The payment service returned an unexpected checkout. Please try again shortly.']);
            }

            $payment->update(['provider_reference' => $reference, 'checkout_url' => $checkoutUrl]);
            $pending?->update(['status' => Payment::STATUS_CANCELLED]);

            return $payment;
        });
    }

    /**
     * @param  array<string, mixed>  $session
     */
    public function applyCheckout(array $session, ?bool $eventLiveMode = null): ?Payment
    {
        $reference = data_get($session, 'id');
        if (! is_string($reference)) {
            abort(422);
        }

        $payment = Payment::query()->where('provider', Payment::PROVIDER_PAYMONGO_QRPH)
            ->where('provider_reference', $reference)->first();
        if ($payment === null) {
            return null;
        }

        $attributes = data_get($session, 'attributes');
        abort_unless(is_array($attributes), 422);
        $liveMode = $attributes['livemode'] ?? $eventLiveMode;
        $validIdentity = $liveMode === $payment->provider_livemode
            && $liveMode === $this->gateway->isLiveMode()
            && ($eventLiveMode === null || $eventLiveMode === $liveMode)
            && ($attributes['reference_number'] ?? null) === $payment->public_id
            && data_get($attributes, 'metadata.payment_id') === $payment->public_id
            && data_get($attributes, 'metadata.service_request_id') === (string) $payment->service_request_id;

        if (! $validIdentity) {
            Log::warning('PayMongo checkout identity did not match.', ['payment_id' => $payment->public_id]);
            abort(422);
        }

        $transactions = $attributes['payments'] ?? [];
        abort_unless(is_array($transactions), 422);
        foreach ($transactions as $transaction) {
            if (data_get($transaction, 'attributes.status') !== 'paid') {
                continue;
            }

            $providerPaymentId = data_get($transaction, 'id');
            $validPayment = is_string($providerPaymentId)
                && preg_match('/\Apay_[a-zA-Z0-9]+\z/', $providerPaymentId)
                && data_get($transaction, 'attributes.amount') === $payment->amountInCentavos()
                && data_get($transaction, 'attributes.currency') === 'PHP'
                && data_get($transaction, 'attributes.source.type') === 'qrph'
                && data_get($transaction, 'attributes.livemode', $liveMode) === $liveMode;

            if (! $validPayment) {
                Log::warning('PayMongo payment details did not match.', ['payment_id' => $payment->public_id]);
                abort(422);
            }

            DB::transaction(function () use ($payment, $providerPaymentId): void {
                $serviceRequest = ServiceRequest::query()->lockForUpdate()->findOrFail($payment->service_request_id);
                $lockedPayment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
                if ($lockedPayment->status === Payment::STATUS_PAID) {
                    abort_unless($lockedPayment->provider_payment_id === $providerPaymentId, 409);

                    return;
                }

                $lockedPayment->update([
                    'status' => Payment::STATUS_PAID,
                    'paid_at' => now(),
                    'provider_payment_id' => $providerPaymentId,
                    'receipt_number' => $providerPaymentId,
                ]);
                $this->certificates->issue($serviceRequest, $lockedPayment);
            });

            return $payment->refresh();
        }

        if (($attributes['status'] ?? null) === 'expired') {
            Payment::query()->whereKey($payment->id)->where('status', Payment::STATUS_PENDING)
                ->update(['status' => Payment::STATUS_EXPIRED]);
        }

        return $payment->refresh();
    }

    public function synchronize(Payment $payment, bool $force = false): void
    {
        if ($payment->provider !== Payment::PROVIDER_PAYMONGO_QRPH
            || $payment->status !== Payment::STATUS_PENDING
            || $payment->provider_reference === null || ! $this->gateway->isConfigured()) {
            return;
        }

        $query = Payment::query()->whereKey($payment->id)->where('status', Payment::STATUS_PENDING);
        if (! $force) {
            $query->where(function (Builder $query): void {
                $query->whereNull('provider_checked_at')->orWhere('provider_checked_at', '<=', now()->subSeconds(30));
            });
        }
        if ($query->update(['provider_checked_at' => now()]) === 0) {
            return;
        }

        $this->refreshFromProvider($payment);
        if ($payment->status === Payment::STATUS_PENDING && $payment->expires_at?->isPast()) {
            $this->close($payment);
        }
    }

    public function close(Payment $payment): void
    {
        if (! $this->gateway->isConfigured()) {
            throw ValidationException::withMessages(['payment' => 'We cannot verify the existing checkout right now. Please contact the barangay office before changing payment methods.']);
        }
        abort_unless($payment->provider_livemode === $this->gateway->isLiveMode(), 422);
        $this->refreshFromProvider($payment);
        if ($payment->status !== Payment::STATUS_PENDING) {
            return;
        }

        $this->gateway->expireCheckout($payment);
        $this->refreshFromProvider($payment);
        if ($payment->status === Payment::STATUS_PENDING) {
            throw ValidationException::withMessages(['payment' => 'The previous checkout is still active. Please try again shortly before changing payment methods.']);
        }
    }

    private function refreshFromProvider(Payment $payment): void
    {
        $session = $this->gateway->retrieveCheckout($payment);
        abort_unless(data_get($session, 'id') === $payment->provider_reference, 422);
        $this->applyCheckout($session);
        $payment->refresh();
    }
}
