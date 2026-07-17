<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'min:2'
                ],

                'email' => [
                    'required',
                    'email',
                    'unique:users,email'
                ],

                'password' => [
                    'required',
                    'min:8',
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'regex:/[\W_]/',
                ],
            ]);

            $user = User::create([
                'name' => trim($validated['name']),
                'email' => strtolower(trim($validated['email'])),
                'password' => $validated['password'],
            ]);

            Auth::login($user);

            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);

        }
        catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        }
        catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'errors' => [
                    'general' => ['Server error']
                ]
            ], 500);

        }
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        try {

            $credentials = $request->validate([
                'email' => ['required','email'],
                'password' => ['required']
            ]);

            $credentials['email'] = strtolower(trim($credentials['email']));

            if (!Auth::attempt($credentials)) {

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);

            }

            $request->session()->regenerate();

            $user = Auth::user();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);

        }
        catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        }
        catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);

        }
    }

    /**
     * Return authenticated user
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'success' => true
        ]);
    }


}