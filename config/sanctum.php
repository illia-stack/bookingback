<?php

use Laravel\Sanctum\Sanctum;

return [

    // kann leer bleiben oder komplett entfernt werden
    'stateful' => explode(
        ',',
        env('SANCTUM_STATEFUL_DOMAINS', '')
    ),

    // bleibt, ist ok
    'guard' => ['web'],

    // optional
    'expiration' => null,

    'token_prefix' => '',

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],
];