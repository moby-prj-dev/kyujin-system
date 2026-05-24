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

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model'   => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    ],

    'recaptcha' => [
        'site_key'   => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],

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
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'google_indexing' => [
        'service_account_json' => env('GOOGLE_INDEXING_SERVICE_ACCOUNT_JSON'),
        'daily_limit'          => (int) env('GOOGLE_INDEXING_DAILY_LIMIT', 100),
        'resubmit_after_days'  => (int) env('GOOGLE_INDEXING_RESUBMIT_DAYS', 30),
    ],

    'pexels' => [
        'api_key' => env('PEXELS_API_KEY'),
    ],

    'estat' => [
        'app_id'                => env('ESTAT_APP_ID'),
        // 賃金構造基本統計調査の最新統計表ID(都道府県・職種別賃金)
        // 年次更新時は ESTAT_WAGE_STATS_DATA_ID を .env で上書き
        'wage_stats_data_id'    => env('ESTAT_WAGE_STATS_DATA_ID', '0003443094'),
        'base_url'              => env('ESTAT_BASE_URL', 'https://api.e-stat.go.jp/rest/3.0/app/json'),
    ],
];
