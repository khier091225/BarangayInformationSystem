<?php

return [
    'public_url' => env('DEMO_PAYMENT_PUBLIC_URL'),
    'expires_minutes' => (int) env('DEMO_PAYMENT_EXPIRES_MINUTES', 60),
];
