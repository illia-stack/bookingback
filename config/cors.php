<?php

return [

    'paths' => [
        'api/*',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://bookingfront-b9j1.onrender.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        '*',
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    // ❌ WICHTIG: muss false sein bei Bearer Token
    'supports_credentials' => false,
];