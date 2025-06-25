<?php

namespace Modules\User\Services;

use App\Exceptions\ResourceNotCreatedException;
use Exception;
use Modules\OAuth\DataTransferObjects\UserDTO;
use Modules\User\Models\User;
use Modules\User\Repositories\UserRepository;

readonly class UserService {
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    /**
     * @param  UserDTO  $userDTO
     * @return User
     * @throws ResourceNotCreatedException
     */
    public function register(UserDTO $userDTO): User {
        try {
            return $this->userRepository->create($userDTO->toModel());
        } catch (Exception $ex) {
            throw new ResourceNotCreatedException("User has not been created", previous: $ex);
        }
    }
}