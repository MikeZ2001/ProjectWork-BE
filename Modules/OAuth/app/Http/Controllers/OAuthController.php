<?php

namespace Modules\OAuth\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Modules\OAuth\Exceptions\AuthenticationFailedException;
use Modules\OAuth\Exceptions\LogoutException;
use Modules\OAuth\Http\Requests\LoginRequest;
use Modules\OAuth\Services\AuthenticationService;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;

/**
 * @group Modules
 * @subgroup OAuth
 */
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
     * @responseFile 201 storage/responses/oauth/login-success.json
     * @responseFile 422 storage/responses/oauth/login-validation-error.json
     * @responseFile 400 storage/responses/oauth/login-failed-exception.json
     * @responseFile 401 storage/responses/oauth/login-unauthorized-exception.json
     * @responseFile 500 storage/responses/oauth/login-error.json
     *
     * @unauthenticated
     *
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Clear any existing cookies first
        Cookie::unqueue('access_token');
        Cookie::unqueue('refresh_token');
        
        $payload = $this->authenticationService->authenticate($request->getDTO());
        
        // Set secure cookies with proper domain and SameSite policy
        $isSecure = request()->isSecure() || app()->environment('production');
        $domain = config('session.domain') ?: null;
        
        return response()->json($payload)
            ->cookie('access_token', $payload['access_token'], 60*24*7, '/', $domain, $isSecure, true, false, $isSecure ? 'None' : 'Lax')
            ->cookie('refresh_token', $payload['refresh_token'], 60*24*7, '/', $domain, $isSecure, true, false, $isSecure ? 'None' : 'Lax');
    }

    /**
     * Logout user (revoke the token)
     *
     * @param  Request  $request
     * @return JsonResponse
     * @throws LogoutException
     * @throws ResourceNotFoundException
     *
     * @responseFile 200 storage/responses/oauth/logout-success.json
     * @responseFile 500 storage/responses/oauth/logout-error.json
     *
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            throw new ResourceNotFoundException("User not found");
        }
        $accessTokenId = $user->token()->id;
        $this->authenticationService->logout($accessTokenId);

        // Clear cookies with proper domain
        $domain = config('session.domain') ?: null;
        
        return response()->json([
            'message' => 'Successfully logged out'
        ])->withCookie(cookie()->forget('access_token', '/', $domain))
            ->withCookie(cookie()->forget('refresh_token', '/', $domain));
    }

    /**
     * Get the authenticated user
     *
     * @param Request $request
     * @return UserResource
     *
     * @responseFile 200 storage/responses/oauth/user-success.json
     */
    public function user(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}