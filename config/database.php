<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection
    |--------------------------------------------------------------------------
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */

    'connections' => [

        /*
        |----------------------------------------------------------------------
        | MySQL (LOCAL / Render / XAMPP / Shared Hosting)
        |----------------------------------------------------------------------
        */

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? [
                PDO::ATTR_TIMEOUT => 5,
            ] : [],
        ],

        /*
        |----------------------------------------------------------------------
        | PostgreSQL (Supabase / Render DB / Cloud DB)
        |----------------------------------------------------------------------
        */

        'pgsql' => [
            'driver' => 'pgsql',

            // Supabase Pooler Host (NICHT normaler DB Host)
            'host' => env('DB_HOST'),

            'port' => env('DB_PORT', '5432'),

            'database' => env('DB_DATABASE'),

            'username' => env('DB_USERNAME'),

            'password' => env('DB_PASSWORD'),

            'charset' => 'utf8',

            'prefix' => '',

            'schema' => 'public',

            'sslmode' => 'require',

            /*
            |--------------------------------------------------------------------------
            | IMPORTANT FOR SUPABASE POOLER
            |--------------------------------------------------------------------------
            |
            | verhindert prepared statement issues mit PgBouncer
            |
            */
            'options' => extension_loaded('pdo_pgsql') ? [
                PDO::ATTR_EMULATE_PREPARES => true,
            ] : [],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Table
    |--------------------------------------------------------------------------
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis (optional, later scaling)
    |--------------------------------------------------------------------------
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_',
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],
    ],

];