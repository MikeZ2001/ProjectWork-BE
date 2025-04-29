<?php

namespace Modules\OAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthCookieMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasCookie('access_token') && !$request->headers->has('Authorization')) {
            $accessToken = $request->cookie('access_token');

            $request->headers->set('Authorization', 'Bearer ' . $accessToken);
        }

        return $next($request);
    }
}