<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::get('/debug/auth', function (Request $request) {

    return response()->json([
        'user' => auth()->user(),
        'check' => auth()->check(),
        'session_id' => session()->getId(),
        'csrf' => csrf_token(),
        'session_all' => session()->all(),
        'cookies' => $request->cookies->all(),
        'header_origin' => $request->header('origin'),
    ]);
});