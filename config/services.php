<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | External API Integrations
    |--------------------------------------------------------------------------
    */

    'marketplace' => [
        'base_url' => env('MARKETPLACE_BASE_URL', 'https://api.marketplace.example.com'),
        'client_id' => env('MARKETPLACE_CLIENT_ID'),
        'client_secret' => env('MARKETPLACE_CLIENT_SECRET'),
        'redirect_uri' => env('MARKETPLACE_REDIRECT_URI'),
        'scope' => env('MARKETPLACE_SCOPE', 'read write'),
        'sandbox' => env('MARKETPLACE_SANDBOX', true),
        'mock' => env('MARKETPLACE_MOCK', true),
        'auth_url' => env('MARKETPLACE_AUTH_URL', '/oauth/authorize'),
        'token_url' => env('MARKETPLACE_TOKEN_URL', '/oauth/token'),
    ],

    'ga4' => [
        'property_id' => env('GA4_PROPERTY_ID'),
        'credentials_path' => env('GA4_CREDENTIALS', 'storage/app/ga4.json'),
        'mock' => env('GA4_MOCK', true),
    ],

];
