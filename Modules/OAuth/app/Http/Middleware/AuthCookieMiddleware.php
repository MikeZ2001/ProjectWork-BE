<?php

namespace Modules\OAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthCookieMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1) Let CORS preflight through without auth
        if ($request->isMethod('OPTIONS')) {
            return response('', 204);
        }

        // 2) If we have the cookie and no Authorization header yet, inject it
        if ($request->hasCookie('access_token') && !$request->bearerToken()) {
            $accessToken = (string) $request->cookie('access_token');
            if ($accessToken !== '') {
                $request->headers->set('Authorization', 'Bearer '.$accessToken);
            }
        }

        // 3) Log token information for debugging (remove in production)
        if (app()->environment('local', 'testing') && $request->hasCookie('access_token')) {
            Log::info('AuthCookieMiddleware: Token found in cookie', [
                'token_length' => strlen($request->cookie('access_token')),
                'has_auth_header' => $request->hasHeader('Authorization'),
                'bearer_token' => $request->bearerToken() ? 'present' : 'missing'
            ]);
        }

        // 4) Don't throw here. Route middleware (auth:api) will decide.
        return $next($request);
    }
}
