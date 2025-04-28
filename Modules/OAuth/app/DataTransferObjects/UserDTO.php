<?php

namespace Modules\OAuth\DataTransferObjects;

use App\DataTransferObjects\EntityDTO;
use Modules\User\app\Models\User;

/**
 * @method User hydrateModel(User $model)
 */
readonly class UserDTO extends EntityDTO
{
    public function __construct(
        protected string $email,
        protected string $password,
        protected string $first_name,
        protected string $last_name,
    ) {
    }

    public function toModel(): User
    {
        return $this->hydrateModel(new User());
    }
}