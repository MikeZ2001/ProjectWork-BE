<?php

namespace Modules\OAuth\Http\Middleware;

use App\Exceptions\ResourceNotFoundException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\OAuth\Exceptions\AuthenticationFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthCookieMiddleware
{
    /**
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasCookie('access_token') && !$request->headers->has('Authorization')) {
            // Get the most recent access_token cookie
            $accessToken = $this->getMostRecentToken($request, 'access_token');

            if ($accessToken) {
                $request->headers->set('Authorization', 'Bearer ' . $accessToken);
                
                try {
                    if (! Auth::guard('api')->check()) {
                        throw new UnauthorizedHttpException('Bearer', 'Token invalid or revoked');
                    }
                } catch (\Exception $e) {
                    // If token is invalid, try to get a different one or fail
                    throw new UnauthorizedHttpException('Bearer', 'Token invalid or expired');
                }
            } else {
                throw new AuthenticationFailedException("No valid access token found");
            }
        } elseif (env('APP_ENV') === 'testing') {
            return $next($request);
        } else {
            throw new AuthenticationFailedException("Unauthorized: unable to set cookie");
        }

        return $next($request);
    }
    
    /**
     * Get the most recent token from cookies (handles multiple cookies with same name)
     */
    private function getMostRecentToken(Request $request, string $cookieName): ?string
    {
        $allCookies = $request->cookies->all();
        $tokens = $allCookies[$cookieName] ?? [];
        
        if (is_array($tokens)) {
            // Multiple cookies - get the last one (most recent)
            return end($tokens);
        } elseif (is_string($tokens)) {
            // Single cookie
            return $tokens;
        }
        
        return null;
    }
}
