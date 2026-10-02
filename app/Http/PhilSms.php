<?php

namespace App\Http;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhilSms
{
    public function normalizePhoneNumber(?string $phoneNumber): ?string
    {
        $number = preg_replace('/[\s()\-]/', '', $phoneNumber ?? '');

        if (! preg_match('/\A(?:0|\+?63)(9[0-9]{9})\z/', $number, $matches)) {
            return null;
        }

        return '63'.$matches[1];
    }

    public function isConfigured(): bool
    {
        return filled(config('services.philsms.url'))
            && filled(config('services.philsms.token'))
            && filled(config('services.philsms.sender_id'));
    }

    /**
     * @return 'submitted'|'failed'|'unconfirmed'
     */
    public function send(string $phoneNumber, string $message): string
    {
        try {
            $response = Http::withToken(config('services.philsms.token'))
                ->asJson()->acceptJson()->connectTimeout(3)->timeout(10)
                ->withOptions(['allow_redirects' => false])
                ->post(rtrim((string) config('services.philsms.url'), '/').'/sms/send', [
                    'sender_id' => config('services.philsms.sender_id'),
                    'recipient' => $phoneNumber,
                    'type' => 'plain',
                    'message' => $message,
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('PhilSMS submission could not be confirmed.', [
                'reason' => 'connection_failed',
                'exception' => $exception::class,
            ]);

            return 'unconfirmed';
        }

        if ($response->clientError() && $response->status() !== 408) {
            Log::warning('PhilSMS rejected a submission.', [
                'http_status' => $response->status(),
                'provider_status' => $response->json('status'),
            ]);

            return 'failed';
        }

        if (! $response->successful()) {
            Log::warning('PhilSMS submission could not be confirmed.', [
                'reason' => 'unexpected_http_status',
                'http_status' => $response->status(),
                'provider_status' => $response->json('status'),
            ]);

            return 'unconfirmed';
        }

        $providerStatus = $response->json('status');
        $deliveryStatus = match ($providerStatus) {
            'success' => 'submitted',
            'error' => 'failed',
            default => 'unconfirmed',
        };

        if ($deliveryStatus !== 'submitted') {
            Log::warning('PhilSMS returned a non-success submission status.', [
                'http_status' => $response->status(),
                'provider_status' => $providerStatus,
            ]);
        }

        return $deliveryStatus;
    }
}
