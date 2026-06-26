<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Origines autorisées à appeler l'API Laravel depuis le frontend Vite.
    | En production, remplacer par le vrai domaine (ex: https://app.smartsms.com).
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://localhost:3000',
        'http://localhost:8000',
        'http://127.0.0.1:8000',
    ],

    'allowed_origins_patterns' => [
        '#^https://.*\.trycloudflare\.com$#', // tunnel temporaire (test paiement mobile)
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    | Requis pour Sanctum SPA (cookies de session cross-origin).
    |
    */
    'supports_credentials' => true,

];
