<?php

use Illuminate\Support\Str;

return [

    'driver' => 'file',

    'lifetime' => 120,

    'expire_on_close' => false,

    'encrypt' => false,

    'files' => storage_path('framework/sessions'),

    'connection' => null,

    'table' => 'sessions',

    'store' => null,

    'lottery' => [2, 100],

    'cookie' => Str::slug('laravel', '_') . '_session',

    'path' => '/',

    // ❌ NICHT mehr Cross-domain konfigurieren
    'domain' => null,

    'secure' => false,

    'http_only' => true,

    'same_site' => 'lax',
];