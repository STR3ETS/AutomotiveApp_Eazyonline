<?php

return [
    'marketplace' => [
        'base_url' => env('MARKETPLACE_API_URL', 'https://api.marketplace.example.com'),
        'client_id' => env('MARKETPLACE_CLIENT_ID'),
        'client_secret' => env('MARKETPLACE_CLIENT_SECRET'),
        'redirect_uri' => env('APP_URL') . '/oauth/marketplace/callback',
        'scopes' => ['listings:write', 'listings:read'],
        'mock' => env('MARKETPLACE_MOCK', true),
        'rate_limit' => [
            'requests_per_minute' => 60,
            'retry_after_seconds' => 60
        ]
    ],

    'ga4' => [
        'property_id' => env('GA4_PROPERTY_ID'),
        'service_account_path' => env('GA4_SERVICE_ACCOUNT_PATH'),
        'mock' => env('GA4_MOCK', true),
        'cache_ttl_minutes' => 15
    ]
];
