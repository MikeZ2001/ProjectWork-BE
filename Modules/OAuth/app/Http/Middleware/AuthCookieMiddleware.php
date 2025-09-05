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
            $accessToken = $request->cookie('access_token');

            $request->headers->set('Authorization', 'Bearer ' . $accessToken);
            if (! Auth::guard('api')->check()) {
                throw new UnauthorizedHttpException('Bearer', 'Token invalid or revoked');
            }
        } elseif (env('APP_ENV') === 'testing') {
            return $next($request);
        } else {
            throw new AuthenticationFailedException("Unauthorized: unable to set cookie");
        }

        return $next($request);
    }
}
