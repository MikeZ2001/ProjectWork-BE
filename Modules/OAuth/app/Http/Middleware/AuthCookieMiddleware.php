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
            $accessToken = $this->getValidAccessToken($request);
            if ($accessToken !== '') {
                $request->headers->set('Authorization', 'Bearer '.$accessToken);
            }
        }

        // 3) Log token information for debugging (remove in production)
        if (app()->environment('local', 'testing') && $request->hasCookie('access_token')) {
            Log::info('AuthCookieMiddleware: Token found in cookie', [
                'token_length' => strlen($this->getValidAccessToken($request)),
                'has_auth_header' => $request->hasHeader('Authorization'),
                'bearer_token' => $request->bearerToken() ? 'present' : 'missing'
            ]);
        }

        // 4) Don't throw here. Route middleware (auth:api) will decide.
        return $next($request);
    }

    /**
     * Get the most recent valid access token from cookies
     */
    private function getValidAccessToken(Request $request): string
    {
        $cookies = $request->cookies->all();
        $accessTokens = [];
        
        // Collect all access_token cookies
        foreach ($cookies as $name => $value) {
            if ($name === 'access_token') {
                if (is_array($value)) {
                    $accessTokens = array_merge($accessTokens, $value);
                } else {
                    $accessTokens[] = $value;
                }
            }
        }
        
        // If we have multiple tokens, try to find the most recent one
        if (count($accessTokens) > 1) {
            // Sort by token length (newer tokens tend to be longer due to different JWT structure)
            usort($accessTokens, function($a, $b) {
                return strlen($b) - strlen($a);
            });
        }
        
        return $accessTokens[0] ?? '';
    }
}
