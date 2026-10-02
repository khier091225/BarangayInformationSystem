<?php

namespace App\Support;

use InvalidArgumentException;

class CertificateFees
{
    /** @var array<string, numeric-string> */
    private const RATES = [
        'Barangay Clearance' => '50.00',
        'Certificate of Residency' => '50.00',
        'Certificate of Indigency' => '0.00',
        'Business Clearance' => '100.00',
    ];

    /**
     * @return array<string, numeric-string>
     */
    public static function rates(): array
    {
        return self::RATES;
    }

    /**
     * @return list<string>
     */
    public static function types(): array
    {
        return array_keys(self::RATES);
    }

    /** @return numeric-string */
    public static function amountFor(string $certificateType): string
    {
        return self::RATES[$certificateType]
            ?? throw new InvalidArgumentException("Unsupported certificate type: {$certificateType}");
    }

    public static function labelFor(string $certificateType): string
    {
        $amount = self::amountFor($certificateType);

        return (float) $amount === 0.0
            ? 'Free'
            : '₱'.number_format((float) $amount, 2);
    }
}
