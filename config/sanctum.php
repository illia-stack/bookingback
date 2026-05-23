<?php

use Laravel\Sanctum\Sanctum;

return [

    // kann leer bleiben oder komplett entfernt werden
    'stateful' => [],

    // bleibt, ist ok
    'guard' => ['web'],

    // optional
    'expiration' => null,

    'token_prefix' => '',

    // 🔥 WICHTIG: Middleware für SPA/Cookies NICHT mehr nötig
    'middleware' => [
        // kann leer bleiben oder minimal
    ],
];