<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionAuthController;

Route::get('/', function () {
    return ['status' => 'API running'];
});


Route::prefix('api')->group(function () {

    Route::get('/csrf', [
        SessionAuthController::class,
        'csrf'
    ]);

    Route::post('/auth/register', [
        SessionAuthController::class,
        'register'
    ]);

    Route::post('/auth/login', [
        SessionAuthController::class,
        'login'
    ]);

    Route::post('/auth/logout', [
        SessionAuthController::class,
        'logout'
    ]);

    Route::get('/me', [
        SessionAuthController::class,
        'me'
    ]);

});