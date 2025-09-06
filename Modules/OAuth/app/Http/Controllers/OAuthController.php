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
        $payload = $this->authenticationService->authenticate($request->getDTO());
        
        // Set secure cookies with proper domain and SameSite policy
        $isSecure = request()->isSecure() || app()->environment('production');
        $domain = config('session.domain') ?: null;
        
        // Create response with new cookies and clear old ones
        $response = response()->json($payload);
        
        // Clear old cookies first (with different domain combinations to ensure cleanup)
        $response->withCookie(cookie()->forget('access_token', '/', null))
                 ->withCookie(cookie()->forget('refresh_token', '/', null))
                 ->withCookie(cookie()->forget('access_token', '/', $domain))
                 ->withCookie(cookie()->forget('refresh_token', '/', $domain))
                 ->withCookie(cookie()->forget('access_token', '/', '.onrender.com'))
                 ->withCookie(cookie()->forget('refresh_token', '/', '.onrender.com'));
        
        // Set new cookies
        $response->withCookie('access_token', $payload['access_token'], 60*24*7, '/', $domain, $isSecure, true, false, $isSecure ? 'None' : 'Lax')
                 ->withCookie('refresh_token', $payload['refresh_token'], 60*24*7, '/', $domain, $isSecure, true, false, $isSecure ? 'None' : 'Lax');
        
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

    /**
     * Debug endpoint to check token information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function debug(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        $cookieToken = $request->cookie('access_token');
        
        return response()->json([
            'has_bearer_token' => !empty($token),
            'has_cookie_token' => !empty($cookieToken),
            'bearer_token_length' => $token ? strlen($token) : 0,
            'cookie_token_length' => $cookieToken ? strlen($cookieToken) : 0,
            'tokens_match' => $token === $cookieToken,
            'user_authenticated' => $request->user() ? true : false,
            'user_id' => $request->user()?->id,
        ]);
    }

    /**
     * Test authentication endpoint - bypasses auth middleware
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function testAuth(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        $cookieToken = $request->cookie('access_token');
        
        // Try to manually authenticate the user
        $user = null;
        $tokenId = null;
        $tokenModel = null;
        
        if ($token) {
            try {
                $tokenId = $this->getTokenId($token);
                if ($tokenId) {
                    $tokenModel = \Laravel\Passport\Token::where('id', $tokenId)->first();
                    $user = $tokenModel?->user;
                }
            } catch (\Exception $e) {
                // Log the error
                \Log::error('Test auth error', ['error' => $e->getMessage()]);
            }
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Test endpoint working',
            'has_bearer_token' => !empty($token),
            'has_cookie_token' => !empty($cookieToken),
            'bearer_token_length' => $token ? strlen($token) : 0,
            'cookie_token_length' => $cookieToken ? strlen($cookieToken) : 0,
            'tokens_match' => $token === $cookieToken,
            'token_id' => $tokenId,
            'token_found_in_db' => $tokenModel ? true : false,
            'token_revoked' => $tokenModel?->revoked ?? null,
            'user_found' => $user ? true : false,
            'user_id' => $user?->id,
            'passport_keys_exist' => [
                'private' => file_exists(storage_path('oauth-private.key')),
                'public' => file_exists(storage_path('oauth-public.key'))
            ]
        ]);
    }

    /**
     * Test user endpoint - uses auth middleware
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function testUser(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'User endpoint working',
            'user_authenticated' => $request->user() ? true : false,
            'user_id' => $request->user()?->id,
            'user_email' => $request->user()?->email,
        ]);
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