<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('user')) {

            return response()->json([
                "message" => "Unauthenticated"
            ], 401);

        }

        return $next($request);
    }
}