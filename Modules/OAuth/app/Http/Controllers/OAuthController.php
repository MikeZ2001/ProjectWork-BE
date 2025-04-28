<?php

namespace Modules\OAuth\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\OAuth\Exceptions\AuthenticationFailedException;
use Modules\OAuth\Exceptions\LogoutException;
use Modules\OAuth\Http\Requests\LoginRequest;
use Modules\OAuth\Services\AuthenticationService;

class OAuthController extends Controller
{
    public function __construct(
        private readonly AuthenticationService $authenticationService
    ) {
    }
    
    /**
     * Handle an authentication attempt.
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     * @throws ResourceNotFoundException
     * @throws AuthenticationFailedException
     *
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $responseContent = $this->authenticationService->authenticate($request->getDTO());
        return response()->json($responseContent)
            ->cookie(
                'access_token',
                $responseContent['access_token'],
                60 * 24 * 7, // 7 days
                '/',
                null,
                true, // secure (only HTTPS)
                true, // HttpOnly
                false,
                'Strict'
            )
            ->cookie(
            'refresh_token',
            $responseContent['refresh_token'],
            60 * 24 * 7, // 7 days
            '/',
            null,
            true, // secure (only HTTPS)
            true, // HttpOnly
            false,
            'Strict'
        );
    }
    
    /**
     * Logout user (revoke the token)
     *
     * @param Request $request
     * @return JsonResponse
     * @throws LogoutException
     */
    public function logout(Request $request): JsonResponse
    {
        $accessTokenId = $request->user()->token()->id;
        $this->authenticationService->logout($accessTokenId);
        
        return response()->json([
            'message' => 'Successfully logged out'
        ])->withCookie(cookie()->forget('access_token'))
            ->withCookie(cookie()->forget('refresh_token'));
    }
    
    /**
     * Get the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
