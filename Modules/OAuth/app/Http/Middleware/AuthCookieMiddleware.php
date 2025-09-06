<?php

namespace Modules\OAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Token;
use Laravel\Passport\Passport;

class AuthCookieMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1) Let CORS preflight through without auth
        if ($request->isMethod('OPTIONS')) {
            return response('', 204);
        }

        // 2) For cross-domain requests, prioritize Authorization header over cookies
        // If we have the cookie and no Authorization header yet, inject it
        // Check both regular and Safari-specific cookies
        if ((!$request->bearerToken()) && 
            ($request->hasCookie('access_token') || $request->hasCookie('access_token_safari'))) {
            $accessToken = $this->getValidAccessToken($request);
            if ($accessToken !== '') {
                $request->headers->set('Authorization', 'Bearer '.$accessToken);
            }
        }

        // 3) Log token information for debugging
        if (app()->environment('production')) {
            Log::info('AuthCookieMiddleware: Token processing', [
                'has_auth_header' => $request->hasHeader('Authorization'),
                'has_cookie_token' => $request->hasCookie('access_token'),
                'bearer_token' => $request->bearerToken() ? 'present' : 'missing',
                'origin' => $request->header('Origin')
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
        
        // Collect all access_token cookies (both regular and Safari-specific)
        foreach ($cookies as $name => $value) {
            if ($name === 'access_token' || $name === 'access_token_safari') {
                if (is_array($value)) {
                    $accessTokens = array_merge($accessTokens, $value);
                } else {
                    $accessTokens[] = $value;
                }
            }
        }
        
        // If we have multiple tokens, validate each one and return the first valid one
        if (count($accessTokens) > 1) {
            foreach ($accessTokens as $token) {
                if ($this->isValidToken($token)) {
                    return $token;
                }
            }
        }
        
        return $accessTokens[0] ?? '';
    }

    /**
     * Check if a token is valid by trying to find it in the database
     */
    private function isValidToken(string $token): bool
    {
        try {
            // Try to find the token in the database
            $tokenModel = Passport::token()->where('id', $this->getTokenId($token))->first();
            return $tokenModel && !$tokenModel->revoked;
        } catch (\Exception $e) {
            // If there's any error, assume token is invalid
            return false;
        }
    }

    /**
     * Extract token ID from JWT token
     */
    private function getTokenId(string $token): ?string
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return null;
            }
            
            $payload = json_decode(base64_decode($parts[1]), true);
            return $payload['jti'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
