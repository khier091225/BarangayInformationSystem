<?php

namespace App\Http;

use App\Models\Payment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PayMongo
{
    public function isLiveMode(): bool
    {
        return config('payments.paymongo.mode') === 'live';
    }

    public function isConfigured(): bool
    {
        $mode = config('payments.paymongo.mode');
        $key = (string) config('payments.paymongo.secret_key');
        $publicUrl = (string) config('payments.public_url');
        $host = parse_url($publicUrl, PHP_URL_HOST);

        return in_array($mode, ['live', 'test'], true)
            && str_starts_with($key, $this->isLiveMode() ? 'sk_live_' : 'sk_test_')
            && filled(config('payments.paymongo.webhook_secret'))
            && filter_var($publicUrl, FILTER_VALIDATE_URL) !== false
            && parse_url($publicUrl, PHP_URL_SCHEME) === 'https'
            && is_string($host)
            && $host !== 'localhost'
            && (! filter_var($host, FILTER_VALIDATE_IP)
                || filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
            && parse_url($publicUrl, PHP_URL_USER) === null
            && parse_url($publicUrl, PHP_URL_QUERY) === null
            && parse_url($publicUrl, PHP_URL_FRAGMENT) === null;
    }

    public function publicUrl(string $routeName, mixed $parameters): string
    {
        return rtrim((string) config('payments.public_url'), '/').route($routeName, $parameters, false);
    }

    /** @return array<string, mixed> */
    public function createCheckout(Payment $payment): array
    {
        $serviceRequest = $payment->serviceRequest;

        return $this->send('POST', '/v2/checkout_sessions', [
            'data' => ['attributes' => [
                'line_items' => [[
                    'name' => $serviceRequest->certificate_type,
                    'amount' => $payment->amountInCentavos(),
                    'currency' => 'PHP',
                    'quantity' => 1,
                ]],
                'payment_method_types' => ['qrph'],
                'reference_number' => $payment->public_id,
                'metadata' => [
                    'payment_id' => $payment->public_id,
                    'service_request_id' => (string) $serviceRequest->id,
                ],
                'success_url' => $this->publicUrl('account.payments.return', $payment),
                'cancel_url' => $this->publicUrl('account.requests.show', $serviceRequest),
                'send_email_receipt' => false,
                'pass_on_fees' => false,
            ]],
        ]);
    }

    /** @return array<string, mixed> */
    public function retrieveCheckout(Payment $payment): array
    {
        return $this->send('GET', '/v1/checkout_sessions/'.$payment->provider_reference);
    }

    public function expireCheckout(Payment $payment): void
    {
        $this->send('POST', '/v1/checkout_sessions/'.$payment->provider_reference.'/expire');
    }

    public function isCheckoutUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && parse_url($url, PHP_URL_SCHEME) === 'https'
            && parse_url($url, PHP_URL_HOST) === 'checkout.paymongo.com'
            && in_array(parse_url($url, PHP_URL_PORT), [null, 443], true)
            && parse_url($url, PHP_URL_USER) === null;
    }

    public function hasValidSignature(string $body, string $signature): bool
    {
        $secret = (string) config('payments.paymongo.webhook_secret');
        $parts = [];
        foreach (explode(',', $signature) as $part) {
            $pair = explode('=', trim($part), 2);
            if (count($pair) === 2) {
                $parts[$pair[0]] = $pair[1];
            }
        }

        $timestamp = $parts['t'] ?? '';
        $provided = $parts[$this->isLiveMode() ? 'li' : 'te'] ?? '';
        if ($secret === '' || ! ctype_digit($timestamp)
            || abs(now()->timestamp - (int) $timestamp) > 300
            || ! preg_match('/\A[a-f0-9]{64}\z/', $provided)) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $timestamp.'.'.$body, $secret), $provided);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function send(string $method, string $path, array $payload = []): array
    {
        try {
            $response = Http::withBasicAuth((string) config('payments.paymongo.secret_key'), '')
                ->acceptJson()->asJson()->connectTimeout(3)->timeout(15)
                ->withOptions(['allow_redirects' => false])
                ->send($method, 'https://api.paymongo.com'.$path, $payload === [] ? [] : ['json' => $payload]);
        } catch (ConnectionException $exception) {
            Log::warning('PayMongo could not be reached.', ['exception' => $exception::class]);
            throw ValidationException::withMessages(['payment' => 'The payment service could not be reached. Please try again shortly.']);
        }

        if (! $response->successful() || ! is_array($response->json('data'))) {
            Log::warning('PayMongo request was unsuccessful.', ['http_status' => $response->status()]);
            throw ValidationException::withMessages(['payment' => 'Online payment is temporarily unavailable. Please try again shortly.']);
        }

        return $response->json('data');
    }
}
