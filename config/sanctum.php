    <?php

    use Laravel\Sanctum\Sanctum;

    return [
        'stateful' => ['bookingfront-b9j1.onrender.com'],

        'guard' => ['web'],

        'expiration' => null,

        'token_prefix' => '',

        'middleware' => [
            'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
            'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
            'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ],

    ];
