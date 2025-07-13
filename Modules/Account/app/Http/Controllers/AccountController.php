<?php

namespace Modules\Account\Http\Controllers;

use App\Exceptions\ResourceNotCreatedException;
use App\Exceptions\ResourceNotDeletedException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ResourceNotUpdatedException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Modules\Account\Http\Requests\AccountRequest;
use Modules\Account\Http\Resources\AccountResource;
use Modules\Account\Models\Account;
use Modules\Account\Services\AccountService;

/**
 * @group Modules
 * @subgroup Account
 */
class AccountController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
    ){
    }

    /**
     * Find all paginated accounts.
     *
     * @queryParam per_page integer The number of items per page. Example: 10
     * @responseFile 200 storage/responses/accounts/index-success.json
     *
     * @authenticated
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->integer('per_page', 10);
        return AccountResource::collection($this->accountService->findAllAndPaginateForUser($perPage));
    }

    /**
     * Store an account.
     *
     * @param  AccountRequest  $request
     * @return AccountResource
     * @throws ResourceNotCreatedException
     *
     * @responseFile 201 storage/responses/accounts/create-success.json
     * @responseFile 422 storage/responses/accounts/create-validation-error.json
     * @responseFile 500 storage/responses/accounts/create-error.json
     */
    public function store(AccountRequest $request): AccountResource
    {
        return new AccountResource($this->accountService->create($request->getDTO()));
    }

    /**
     * Find an account.
     *
     * @param  int  $id
     * @return AccountResource
     * @throws ResourceNotFoundException
     *
     * @responseFile 200 storage/responses/accounts/show-success.json
     * @responseFile 404 storage/responses/accounts/not-found.json
     *
     * @urlParam id integer required The ID of the account. Example: 1
     */
    public function show(int $id): AccountResource
    {
        return new AccountResource($this->accountService->find($id));
    }

    /**
     * Update an account.
     *
     * @param  AccountRequest  $request
     * @param  int  $id
     * @return AccountResource
     * @throws ResourceNotFoundException
     * @throws ResourceNotUpdatedException
     *
     * @responseFile 200 storage/responses/accounts/update-success.json
     * @responseFile 422 storage/responses/accounts/update-validation-error.json
     * @responseFile 404 storage/responses/accounts/not-found.json
     * @responseFile 500 storage/responses/accounts/update-error.json
     *
     * @urlParam id integer required The ID of the account. Example: 1
     */
    public function update(AccountRequest $request, int $id): AccountResource
    {
        return new AccountResource($this->accountService->update($id, $request->getDTO()));
    }

    /**
     * Destroy an account
     *
     * @throws ResourceNotFoundException
     * @throws ResourceNotDeletedException
     *
     * @response 204
     * @responseFile 404 storage/responses/accounts/not-found.json
     * @responseFile 500 storage/responses/accounts/delete-error.json
     *
     * @urlParam id integer required The ID of the account. Example: 1
     */
    public function destroy(int $id): Response
    {
        $this->accountService->delete($id);
        return response()->noContent();
    }
}
