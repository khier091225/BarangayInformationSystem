<?php

return [
    'public_url' => env('PAYMENT_PUBLIC_URL'),
    'expires_minutes' => (int) env('PAYMENT_EXPIRES_MINUTES', 60),
    'paymongo' => [
        'mode' => env('PAYMONGO_MODE', 'live'),
        'secret_key' => env('PAYMONGO_SECRET_KEY'),
        'webhook_secret' => env('PAYMONGO_WEBHOOK_SECRET'),
    ],
];
