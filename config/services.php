<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    /*
    |--------------------------------------------------------------------------
    | GitHub
    |--------------------------------------------------------------------------
    |
    | Dipakai fitur sinkronisasi repository (FR-14). Token bersifat opsional:
    | tanpa token tetap bisa menarik data publik, namun batas request GitHub
    | lebih kecil (rate limit). Simpan token di .env, jangan di kode.
    |
    */

    'github' => [
        'username' => env('GITHUB_USERNAME', 'nailzen014-collab'),
        'token' => env('GITHUB_TOKEN'),
        'cache_minutes' => (int) env('GITHUB_CACHE_MINUTES', 60),
    ],

];
