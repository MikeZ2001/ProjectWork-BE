<?php

namespace App\Guards;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Laravel\Passport\Token;
use Laravel\Passport\Passport;

class CustomPassportGuard implements Guard
{
    use GuardHelpers;

    protected $request;
    protected $provider;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $token = $this->getTokenForRequest();

        if (!empty($token)) {
            $user = $this->authenticateViaToken($token);
        }

        return $this->user = $user;
    }

    public function validate(array $credentials = [])
    {
        return false;
    }

    protected function getTokenForRequest()
    {
        $token = $this->request->bearerToken();

        if (empty($token)) {
            $token = $this->request->cookie('access_token');
        }

        return $token;
    }

    protected function authenticateViaToken($token)
    {
        try {
            // Extract token ID from JWT
            $tokenId = $this->getTokenId($token);
            
            if (!$tokenId) {
                \Log::info('No token ID found in JWT');
                return null;
            }

            \Log::info('Looking for token in database', ['token_id' => $tokenId]);

            // Find token in database
            $tokenModel = Passport::token()->where('id', $tokenId)->first();

            if (!$tokenModel) {
                \Log::info('Token not found in database', ['token_id' => $tokenId]);
                return null;
            }

            if ($tokenModel->revoked) {
                \Log::info('Token is revoked', ['token_id' => $tokenId]);
                return null;
            }

            // Get user from token
            $user = $tokenModel->user;

            if (!$user) {
                \Log::info('No user associated with token', ['token_id' => $tokenId]);
                return null;
            }

            \Log::info('User found via token', ['user_id' => $user->id, 'token_id' => $tokenId]);
            return $user;

        } catch (\Exception $e) {
            \Log::error('Token authentication failed', [
                'error' => $e->getMessage(),
                'token_length' => strlen($token)
            ]);
            return null;
        }
    }

    protected function getTokenId(string $token): ?string
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
