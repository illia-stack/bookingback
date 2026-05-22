<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'user' => $user
        ]);
    }

    // LOGIN (SANCTUM COOKIE)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {

            \Log::info('LOGIN FAILED', [
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);

            \Log::info('SESSION CSRF TOKEN', [
                'csrf_session' => csrf_token(),
                'csrf_header' => $request->header('X-XSRF-TOKEN'),
            ]);

            \Log::info('SESSION DEBUG', [
                'session_id' => session()->getId(),
                'cookie_session' => $request->cookie(config('session.cookie')),
            ]);

            \Log::info('AUTH CHECK', [
                'check' => Auth::check(),
                'id' => Auth::id(),
            ]);

            \Log::info('REQUEST META', [
                'origin' => $request->headers->get('origin'),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $request->session()->regenerate();

        \Log::info('LOGIN SUCCESS', [
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
            'session' => session()->all(),
            'cookies' => $request->cookies->all(),
            'x_xsrf_token_header' => $request->header('X-XSRF-TOKEN'),
        ]);

        return response()->json([
            'user' => $request->user()
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out'
        ]);
    }
}