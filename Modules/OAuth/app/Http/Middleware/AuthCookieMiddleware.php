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

        // 2) Check for any valid access token cookie and inject Authorization header
        if (!$request->bearerToken()) {
            $accessToken = $this->getValidAccessToken($request);
            if ($accessToken !== '') {
                $request->headers->set('Authorization', 'Bearer '.$accessToken);
            }
        }

        // 3) Log token information for debugging
        if (app()->environment('production')) {
            Log::info('AuthCookieMiddleware: Token processing', [
                'has_auth_header' => $request->hasHeader('Authorization'),
                'has_primary_cookie' => $request->hasCookie('access_token'),
                'has_safari_cookie' => $request->hasCookie('access_token_safari'),
                'has_domain_cookie' => $request->hasCookie('access_token_domain'),
                'bearer_token' => $request->bearerToken() ? 'present' : 'missing',
                'origin' => $request->header('Origin'),
                'user_agent' => $request->header('User-Agent')
            ]);
        }

        // 4) Don't throw here. Route middleware (auth:api) will decide.
        return $next($request);
    }

    /**
     * Get the most recent valid access token from cookies
     * Checks multiple cookie strategies for cross-browser compatibility
     */
    private function getValidAccessToken(Request $request): string
    {
        // Priority order: primary cookies first, then fallbacks
        $cookieNames = [
            'access_token',           // Primary universal cookie
            'access_token_safari',    // Safari fallback
            'access_token_domain'     // Domain-specific fallback
        ];
        
        foreach ($cookieNames as $cookieName) {
            if ($request->hasCookie($cookieName)) {
                $token = $request->cookie($cookieName);
                if ($token && $this->isValidToken($token)) {
                    return $token;
                }
            }
        }
        
        return '';
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
