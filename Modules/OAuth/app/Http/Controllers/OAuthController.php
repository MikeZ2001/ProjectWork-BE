<?php

namespace Modules\OAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\OAuth\Http\Requests\LoginRequest;
use Modules\OAuth\Services\AuthenticationService;
use App\Models\User;

class OAuthController extends Controller
{
    public function __construct(private readonly AuthenticationService $authenticationService) {
    }
    
    /**
     * Register a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role
        ]);
        
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }
    
    /**
     * Handle an authentication attempt.
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     *
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $responseContent = $this->authenticationService->authenticate($request->getDTO());
        return response()->json($responseContent);
    }
    
    /**
     * Logout user (revoke the token)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
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
