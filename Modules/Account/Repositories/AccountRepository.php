<?php

namespace Modules\Account\Repositories;

use App\Repositories\EloquentRepository;
use Modules\Account\Models\Account;

class AccountRepository extends EloquentRepository
{
    public function __construct()
    {
        return $this->modelClass = Account::class;
    }
}