<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminReportController;

/*
|--------------------------------------------------------------------------
| AUTH (Public)
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    Route::middleware('throttle:5,1')->post('/register', [AuthController::class, 'register']);
    Route::middleware('throttle:5,1')->post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

Route::post('/contact', [ContactController::class, 'send']);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (SANCTUM COOKIE AUTH)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Current user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | PROPERTIES (AUTH REQUIRED)
    |--------------------------------------------------------------------------
    */

    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | BOOKINGS (AUTH REQUIRED)
    |--------------------------------------------------------------------------
    */

    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN (AUTH + ADMIN MIDDLEWARE)
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin')->group(function () {

        Route::get('/admin/users', [AdminReportController::class, 'index']);

        Route::get('/admin/export-bookings', [AdminReportController::class, 'exportBookings']);
    });
});