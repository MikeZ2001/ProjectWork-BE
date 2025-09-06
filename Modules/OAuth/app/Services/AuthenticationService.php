<?php

namespace Modules\OAuth\Services;

use App\Exceptions\ResourceNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;
use Modules\OAuth\DataTransferObjects\AuthenticationDTO;
use Modules\OAuth\Events\LoginSuccess;
use Modules\OAuth\Exceptions\AuthenticationFailedException;
use Modules\OAuth\Exceptions\LogoutException;
use Symfony\Component\HttpFoundation\Response;

readonly class AuthenticationService {
    
    public function __construct(
        private ClientService $clientService,
        private RefreshTokenRepository $refreshTokenRepository,
        private TokenRepository $tokenRepository,
    ){
    }
    
    /**
     * Authenticate a user to the system and generate an authorization token.
     *
     * @param AuthenticationDTO $authenticationDTO
     *
     * @return array<string, mixed>
     *
     * @throws ResourceNotFoundException
     * @throws AuthenticationFailedException
     *
     */
    public function authenticate(AuthenticationDTO $authenticationDTO): array
    {
        $client = $this->getClient();
        request()->request->add([
            'username' => $authenticationDTO->getEmail(),
            'password' => $authenticationDTO->getPassword(),
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
            'scope' => ''
        ]);
        $tokenRequest = Request::create('/oauth/token', 'POST');
        $response = Route::dispatch($tokenRequest);
        LoginSuccess::dispatch($response);
        return $this->processOAuthTokenResponse($response);
    }
    
    /**
     * Revokes the access and refresh tokens to log out the user.
     *
     * @param string $accessTokenId
     * @return void
     * @throws LogoutException
     */
    public function logout(string $accessTokenId): void
    {
        $response = $this->tokenRepository->revokeAccessToken($accessTokenId);
        if ($response !== 1) {
            throw new LogoutException("Access token not revoked");
        }
        $response = $this->refreshTokenRepository->revokeRefreshTokensByAccessTokenId($accessTokenId);
        if ($response !== 1) {
            throw new LogoutException("Refresh token not revoked");
        }
    }
    
    
    /**
     * Decode OAuth's authentication process response and look for errors.
     *
     * @param Response $response
     *
     * @return array<string, mixed>
     *
     * @throws ResourceNotFoundException
     * @throws AuthenticationFailedException
     */
    private function processOAuthTokenResponse(Response $response): array
    {
        /** @var array<string, mixed> $responseContent */
        $responseContent = json_decode(strval($response->getContent()), true);
        if ($response->getStatusCode() !== 200) {
            /** @var string $errorDescription */
            $errorDescription = $responseContent['error_description'] ?? '';
            if ($response->getStatusCode() === 404) {
                throw new ResourceNotFoundException($errorDescription);
            }
            throw new AuthenticationFailedException($errorDescription);
        }
        return $responseContent;
    }
    
    private function getClient(): Client
    {
        return $this->clientService->findPasswordClient();
    }

}