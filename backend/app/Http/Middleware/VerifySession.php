<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySession
{
    public function handle(Request $request, Closure $next)
    {
        // Verifique se o cookie PHPSESSID está presente
        if (!$request->hasCookie('PHPSESSID')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
