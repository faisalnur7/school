<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CORS Paths
    |--------------------------------------------------------------------------
    |
    | These are the routes that should receive CORS headers. Keep this focused
    | on the API and auth endpoints that may be called from another origin.
    |
    */
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'broadcasting/auth',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Explicit origins are safer than a wildcard when credentials are enabled.
    | The regex patterns cover localhost and the gcscedu.com domain family.
    |
    */
    'allowed_origins' => array_filter(array_map('trim', explode(',', env(
        'CORS_ALLOWED_ORIGINS',
        'http://localhost,http://localhost:3000,http://127.0.0.1,http://127.0.0.1:8000'
    )))),

    'allowed_origins_patterns' => [
        '#^https?://([a-z0-9-]+\.)?gcscedu\.com(?::\d+)?$#i',
        '#^https?://localhost(?::\d+)?$#i',
        '#^https?://127\.0\.0\.1(?::\d+)?$#i',
        '#^https?://\[::1\](?::\d+)?$#i',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers / Exposed Headers
    |--------------------------------------------------------------------------
    */
    'allowed_headers' => ['*'],
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Cache / Credentials
    |--------------------------------------------------------------------------
    */
    'max_age' => 0,
    'supports_credentials' => true,
];
