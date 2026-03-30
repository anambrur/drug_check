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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'public' => env('STRIPE_PUBLIC'),
    ],

    'quest' => [

        // API credentials — required, no fallback defaults
        'username'    => env('QUEST_USERNAME'),
        'password'    => env('QUEST_PASSWORD'),
        'lab_account' => env('QUEST_LAB_ACCOUNT'),
        'dot_lab_account' => env('QUEST_DOT_LAB_ACCOUNT'),

        // API endpoint URLs
        'urls' => [
            'production' => 'https://ocs.questdiagnostics.com/services/ESPService.asmx',
            'staging'    => 'https://qcs-uat.questdiagnostics.com/services/ESPService.asmx',
        ],

        // cURL timeout settings (seconds)
        'timeouts' => [
            'connect' => 60,
            'request' => 60,
        ],

        // SSL verification — disable ONLY in local development against a sandbox
        'ssl' => [
            'verify_peer' => env('QUEST_SSL_VERIFY', true),
            'ca_bundle'   => env('QUEST_CA_BUNDLE'),  // null = use system CA bundle
        ],

        // SOAP WSDL on-disk cache (avoids fetching WSDL on every request)
        'wsdl_cache' => [
            'path' => storage_path('app/wsdl'),
            'ttl'  => 86400, // seconds — 24 hours
        ],

    ],




];
