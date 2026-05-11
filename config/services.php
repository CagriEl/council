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
        'enabled' => (bool) env('TURNSTILE_ENABLED', true),
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
        'verify_url' => env('TURNSTILE_VERIFY_URL', 'https://challenges.cloudflare.com/turnstile/v0/siteverify'),
    ],

    'e_odeme' => [
        'wsdl' => env('E_ODEME_WSDL'),
        // BELSIS ayrı uçlar (çoğunlukla borç sorgusu tahakkuk WSDL üzerinden; tahsilat ödeme akışı içindir)
        'tahakkuk_wsdl' => env('E_ODEME_TAHAKKUK_WSDL'),
        'tahsilat_wsdl' => env('E_ODEME_TAHSILAT_WSDL'),
        'location' => env('E_ODEME_LOCATION'),
        'uri' => env('E_ODEME_URI'),
        'banka_kodu' => env('E_ODEME_BANKA_KODU'),
        'banka_sifresi' => env('E_ODEME_BANKA_SIFRESI'),
        'kurum_kodu' => env('E_ODEME_KURUM_KODU'),
        'borc_sorgula_method' => env('E_ODEME_BORC_SORGULA_METHOD', 'borcSorgula'),
        'timeout' => env('E_ODEME_TIMEOUT', 20),
        // Üretimde true bırakın; yalnızca geçersiz sertifikalı test ortamlarında false
        'soap_verify_ssl' => (bool) env('E_ODEME_SOAP_VERIFY_SSL', true),
        'rate_limit_ip_attempts' => (int) env('E_ODEME_RATE_LIMIT_IP_ATTEMPTS', 60),
        'rate_limit_id_attempts' => (int) env('E_ODEME_RATE_LIMIT_ID_ATTEMPTS', 30),
        'rate_limit_decay_minutes' => (int) env('E_ODEME_RATE_LIMIT_DECAY_MINUTES', 10),
    ],

];
