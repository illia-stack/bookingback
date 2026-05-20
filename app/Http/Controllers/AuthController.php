<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 🟢 REGISTER
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

        // Token für HttpOnly-Cookie
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['user' => $user])
            ->cookie(
                'token', $token,
                60*24,
                '/',
                env('SESSION_DOMAIN', null),
                true,  // HTTPS only
                true   // HttpOnly
            );
    }

    // 🔵 LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user(); // Sicherer als nochmal Query

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['user' => $user])
            ->cookie(
                'token', $token, 
                60*24, // 1 Tag
                '/', 
                env('SESSION_DOMAIN', null), // Optional für Subdomains
                true,  // Secure, nur HTTPS
                true   // HttpOnly
            );
    }

    // 🔴 LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out'
        ])
        ->cookie('token', '', -1, '/', env('SESSION_DOMAIN', null), true, true); // Cookie löschen
    }
}