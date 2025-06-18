<?php

namespace Modules\User\Repositories;

use App\Repositories\EloquentRepository;
use Modules\User\Models\User;

/**
 * @method User create(User $model)
 */
class UserRepository extends EloquentRepository
{
    public function __construct()
    {
        $this->modelClass = User::class;
    }
}