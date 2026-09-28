<?php

namespace App\Http;

class RegistrationCodeSms
{
    public function __construct(public PhilSms $sms) {}

    public function normalizePhoneNumber(?string $phoneNumber): ?string
    {
        return $this->sms->normalizePhoneNumber($phoneNumber);
    }

    public function isConfigured(): bool
    {
        return $this->sms->isConfigured();
    }

    /**
     * @return 'submitted'|'failed'|'unconfirmed'
     */
    public function send(string $phoneNumber, string $code): string
    {
        return $this->sms->send($phoneNumber, "Your barangay registration code is {$code}. Valid for 24 hours, one use only. Do not share this code.");
    }
}
