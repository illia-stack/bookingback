<?php

return [
    'paths' => [
        'api/*',
        'auth/*',
        'sanctum/csrf-cookie', // CSRF Cookie freigeben
    ],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://bookingfront-b9j1.onrender.com', // Frontend Domain
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // VERY IMPORTANT
];