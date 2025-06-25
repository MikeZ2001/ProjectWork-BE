<?php

namespace Modules\OAuth\Services;

use Laravel\Passport\Client;

class ClientService {
    
    public function findPasswordClient(): Client
    {
        return Client::query()->where([
                'password_client' => 1,
                'revoked' => 0
            ])->first();
    }
}