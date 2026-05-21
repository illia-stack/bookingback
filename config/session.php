<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Session Driver
    |--------------------------------------------------------------------------
    |
    | Hier definieren wir, wo die Session gespeichert wird. Standard: Datei.
    |
    */
    'driver' => 'database',

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    | Lebensdauer der Session in Minuten.
    |
    */
    'lifetime' => 120,

    'expire_on_close' => false,

    'encrypt' => false,

    /*
    |--------------------------------------------------------------------------
    | Session File Location
    |--------------------------------------------------------------------------
    */
    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Datenbank / Cache Optionen
    |--------------------------------------------------------------------------
    */
    'connection' => null,
    'table' => 'sessions',
    'store' => null,

    /*
    |--------------------------------------------------------------------------
    | Session Lottery
    |--------------------------------------------------------------------------
    |
    | Kontrolliert, wie oft alte Sessions gelöscht werden.
    |
    */
    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Cookie Options
    |--------------------------------------------------------------------------
    */
    'cookie' => Str::slug('laravel', '_') . '_session', // Cookie-Name

    'path' => '/',

    'domain' => env('SESSION_DOMAIN', null), // Cross-subdomain

    'secure' => env('SESSION_SECURE_COOKIE', false),            // HTTPS nötig

    'http_only' => true,         // Cookie nur HTTP

    'same_site' => 'none',       // Cross-site Cookies
];