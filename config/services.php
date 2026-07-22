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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'turnstile' => [
        // Public site key (safe to expose in HTML)
        'site_key' => env('TURNSTILE_SITE_KEY', '0x4AAAAAAD7TfR2lgr5K6EE1'),
        // Server-only — set TURNSTILE_SECRET in .env (never commit)
        'secret_key' => env('TURNSTILE_SECRET'),
    ],

    'contact' => [
        'notify_to' => env('CONTACT_NOTIFY_TO', 'kirklareli@kirklareli.bel.tr'),
    ],

];
