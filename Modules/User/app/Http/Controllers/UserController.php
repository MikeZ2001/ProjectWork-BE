<?php

namespace Modules\User\Http\Controllers;

use App\Exceptions\ResourceNotCreatedException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\OAuth\Http\Requests\UserRequest;
use Modules\User\Services\UserService;

/**
 * @group Modules
 * @subgroup User
 */
class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * Register a new user
     *
     * @param  UserRequest  $request
     * @return JsonResponse
     * @throws ResourceNotCreatedException
     *
     * @responseFile 201 storage/responses/users/user-create-success.json
     * @responseFile 422 storage/responses/users/user-create-validation-error.json
     * @responseFile 500 storage/responses/users/user-create-error.json
     *
     * @unauthenticated
     */
    public function register(UserRequest $request): JsonResponse
    {
        $this->userService->register($request->getDTO());
        return response()->json([
            'message' => 'User registered successfully',
        ], 201);
    }
}
