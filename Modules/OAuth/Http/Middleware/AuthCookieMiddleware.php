<?php

namespace Modules\OAuth\Http\Middleware;

use App\Exceptions\ResourceNotFoundException;
use Closure;
use Illuminate\Http\Request;
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
        } else {
            throw new AuthenticationFailedException("Unauthorized: unable to set cookie");
        }

        return $next($request);
    }
}