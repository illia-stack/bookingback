<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// AUTH ROUTES für Sanctum Cookie Auth
Route::prefix('auth')->group(function () {

    Route::middleware('throttle:5,1')->group(function () {

        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
Route::middleware('auth:sanctum')->get('/auth/user', function () {
    return response()->json(['user' => Auth::user()]);
});