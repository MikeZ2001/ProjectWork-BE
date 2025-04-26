<?php

namespace Modules\OAuth\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthCookieMiddleware {
    
    public function handle(Request $request, \Closure $next)
    {
        if ($request->hasCookie('access_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('access_token'));
        } else {
            throw new UnauthorizedHttpException('cookies', trans('oauth::messages.cookies.unauthorized'));
        }
        
        return $next($request);
    }

}