<?php

return [
    'alerts' => [
        'tanod' => [
            'phone' => env('INCIDENT_ALERT_TANOD_PHONE'),
            'email' => env('INCIDENT_ALERT_TANOD_EMAIL'),
        ],
        'maintenance' => [
            'phone' => env('INCIDENT_ALERT_MAINTENANCE_PHONE'),
            'email' => env('INCIDENT_ALERT_MAINTENANCE_EMAIL'),
        ],
        'admin' => [
            'phone' => env('INCIDENT_ALERT_ADMIN_PHONE'),
            'email' => env('INCIDENT_ALERT_ADMIN_EMAIL'),
        ],
        'leadership' => [
            'phone' => env('INCIDENT_ALERT_LEADERSHIP_PHONE'),
            'email' => env('INCIDENT_ALERT_LEADERSHIP_EMAIL'),
        ],
    ],
];
