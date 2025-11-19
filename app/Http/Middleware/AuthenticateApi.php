<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApi
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if (!Auth::guard($guards[0] ?? null)->check()) {
            throw new AuthenticationException('Unauthenticated.');
        }

        return $next($request);
    }
}
