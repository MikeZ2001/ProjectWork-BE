<?php

namespace Modules\User\app\Repositories;

use App\Repositories\EloquentRepository;
use Modules\User\app\Models\User;

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