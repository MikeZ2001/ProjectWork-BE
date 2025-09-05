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
        $responseContent = $this->authenticationService->authenticate($request->getDTO());

        // Use Laravel's cookie helper with explicit domain and Partitioned attribute
        $response = response()->json($responseContent);
        
        // Set cookies using Laravel's cookie helper (more reliable than raw headers)
        $response->withCookie(cookie(
            'access_token',
            $responseContent['access_token'],
            60 * 24 * 7, // 7 days
            '/',
            null, // host-only cookie (no domain)
            true, // secure
            true, // httpOnly
            false, // raw
            'none' // sameSite
        ));
        
        $response->withCookie(cookie(
            'refresh_token',
            $responseContent['refresh_token'],
            60 * 24 * 7, // 7 days
            '/',
            null, // host-only cookie (no domain)
            true, // secure
            true, // httpOnly
            false, // raw
            'none' // sameSite
        ));
        
        // Add Partitioned attribute via raw headers (Laravel doesn't support it natively)
        $response->headers->set('Set-Cookie', [
            'access_token='.$responseContent['access_token'].'; Path=/; Max-Age=604800; Secure; HttpOnly; SameSite=None; Partitioned',
            'refresh_token='.$responseContent['refresh_token'].'; Path=/; Max-Age=604800; Secure; HttpOnly; SameSite=None; Partitioned'
        ], false);

        return $response;
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
        
        return response()->json([
            'message' => 'Successfully logged out'
        ])->withCookie(cookie()->forget('access_token'))
            ->withCookie(cookie()->forget('refresh_token'));
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
