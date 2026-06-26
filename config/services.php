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

    // ── Mode simulation paiement ───────────────────────────────────────────────
    // Mettre PAYMENT_SIMULATION_MODE=true pour tester sans vraies API
    'payment' => [
        'simulation_mode' => env('PAYMENT_SIMULATION_MODE', false),
    ],

    // ── Paiement par carte Visa/Mastercard (CinetPay) ─────────────────────────
    // Inscription : https://cinetpay.com — supporte la Guinée (GNF)
    'cinetpay' => [
        'api_key'    => env('CINETPAY_API_KEY'),
        'site_id'    => env('CINETPAY_SITE_ID'),
        'secret_key' => env('CINETPAY_SECRET_KEY'),
        'env'        => env('CINETPAY_ENV', 'sandbox'),
    ],

    // ── Orange Money Guinée (WebPay API) ───────────────────────────────────────
    // Inscription : https://developer.orange.com — produit "OM Webpay Guinea"
    // NOTE : credentials différents de ceux du service Orange SMS
    'orange_money' => [
        'client_id'     => env('ORANGE_MONEY_CLIENT_ID'),
        'client_secret' => env('ORANGE_MONEY_CLIENT_SECRET'),
        'merchant_key'  => env('ORANGE_MONEY_MERCHANT_KEY'),
        'env'           => env('ORANGE_MONEY_ENV', 'sandbox'),
    ],

    // ── Wave ───────────────────────────────────────────────────────────────────
    // Inscription : https://www.wave.com/en/business — disponible en Guinée
    'wave' => [
        'api_key' => env('WAVE_API_KEY'),
    ],

    // ── MTN Mobile Money (Collections API) ────────────────────────────────────
    // Inscription : https://momodeveloper.mtn.com
    'mtn_momo' => [
        'api_user'         => env('MTN_MOMO_API_USER'),
        'api_key'          => env('MTN_MOMO_API_KEY'),
        'subscription_key' => env('MTN_MOMO_SUBSCRIPTION_KEY'),
        'env'              => env('MTN_MOMO_ENV', 'sandbox'),
    ],

    'whatsapp' => [
        'provider' => env('WHATSAPP_PROVIDER', 'meta'),
        'meta' => [
            'access_token'    => env('WHATSAPP_META_TOKEN'),
            'phone_number_id' => env('WHATSAPP_META_PHONE_ID'),
        ],
        'twilio' => [
            'account_sid' => env('TWILIO_SID'),
            'auth_token'  => env('TWILIO_TOKEN'),
            'from_number' => env('TWILIO_WHATSAPP_FROM'),
        ],
    ],

    'orange' => [
        'client_id'            => env('ORANGE_CLIENT_ID'),
        'client_secret'        => env('ORANGE_CLIENT_SECRET'),
        'sender_address'       => env('ORANGE_SENDER_ADDRESS', 'tel:+0000'),
        'sender_name'          => env('ORANGE_SENDER_NAME', 'SmartSMS'),
        'default_country_code' => env('ORANGE_DEFAULT_COUNTRY_CODE', '224'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI'),
    ],

    'linkedin-openid' => [
        'client_id' => env('LINKEDIN_OPENID_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_OPENID_CLIENT_SECRET'),
        'redirect' => env('LINKEDIN_OPENID_REDIRECT_URI'),
    ],


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

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

    // ── Gateway actif ─────────────────────────────────────────────────────────
    // Changer PAYMENT_GATEWAY dans .env pour switcher de provider
    'payment_gateway' => [
        'driver' => env('PAYMENT_GATEWAY', 'lengopay'),
    ],

    // ── Firebase FCM (notifications push) ────────────────────────────────────
    'firebase' => [
        'credentials' => env('FIREBASE_CREDENTIALS'),   // chemin absolu vers service-account.json
        'project_id'  => env('FIREBASE_PROJECT_ID'),
    ],

    'lengopay' => [
        'site_id'            => env('LENGOPAY_SITE_ID'),
        'license_key'        => env('LENGOPAY_LICENSE_KEY'),
        'env'                => env('LENGOPAY_ENV', 'sandbox'),
        'sandbox_url'        => env('LENGOPAY_SANDBOX_URL', 'https://sandbox.lengopay.com/api/v1/payments'),
        'prod_url'           => env('LENGOPAY_PROD_URL',    'https://api.lengopay.com/api/v1/payments'),
        'status_sandbox_url' => env('LENGOPAY_STATUS_SANDBOX_URL', 'https://sandbox.lengopay.com/api/v1/transaction/status'),
        'status_prod_url'    => env('LENGOPAY_STATUS_PROD_URL',    'https://api.lengopay.com/api/v1/transaction/status'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
