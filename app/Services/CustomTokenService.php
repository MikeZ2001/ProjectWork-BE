<?php

namespace App\Services;

use Laravel\Passport\Token;
use Laravel\Passport\Client;
use Modules\User\Models\User;
use Illuminate\Support\Facades\DB;

class CustomTokenService
{
    /**
     * Create and store a token manually in the database
     */
    public function createTokenForUser(User $user, Client $client): array
    {
        try {
            // Generate a unique token ID
            $tokenId = $this->generateTokenId();
            
            // Create the token in the database
            $token = Token::create([
                'id' => $tokenId,
                'user_id' => $user->id,
                'client_id' => $client->id,
                'name' => 'API Token',
                'scopes' => '[]',
                'revoked' => false,
                'created_at' => now(),
                'updated_at' => now(),
                'expires_at' => now()->addDays(15),
            ]);

            // Generate JWT token with the stored ID
            $jwtToken = $this->generateJWTToken($user, $client, $tokenId);
            
            return [
                'access_token' => $jwtToken,
                'token_type' => 'Bearer',
                'expires_in' => 1296000, // 15 days
                'refresh_token' => $this->generateRefreshToken(),
            ];

        } catch (\Exception $e) {
            \Log::error('Custom token creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'client_id' => $client->id
            ]);
            throw $e;
        }
    }

    /**
     * Generate a unique token ID
     */
    private function generateTokenId(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Generate a simple JWT-like token (for testing)
     */
    private function generateJWTToken(User $user, Client $client, string $tokenId): string
    {
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode(json_encode([
            'iss' => config('app.url'),
            'aud' => $client->id,
            'jti' => $tokenId,
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + (15 * 24 * 60 * 60), // 15 days
            'sub' => $user->id,
            'scopes' => []
        ]));
        
        // Simple signature (in production, use proper JWT signing)
        $signature = base64_encode(hash_hmac('sha256', $header . '.' . $payload, 'secret', true));
        
        return $header . '.' . $payload . '.' . $signature;
    }

    /**
     * Generate a refresh token
     */
    private function generateRefreshToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Find user by token ID
     */
    public function findUserByTokenId(string $tokenId): ?User
    {
        try {
            $token = Token::where('id', $tokenId)
                         ->where('revoked', false)
                         ->where('expires_at', '>', now())
                         ->first();

            return $token?->user;
        } catch (\Exception $e) {
            \Log::error('Token lookup failed', [
                'error' => $e->getMessage(),
                'token_id' => $tokenId
            ]);
            return null;
        }
    }
}
