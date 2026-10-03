<?php

return [
    'public_url' => env('PAYMENT_PUBLIC_URL'),
    'expires_minutes' => (int) env('PAYMENT_EXPIRES_MINUTES', 60),
];
