<?php

namespace Modules\Account\Http\Controllers;

use App\Exceptions\ResourceNotCreatedException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ResourceNotUpdatedException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Account\Http\Requests\AccountRequest;
use Modules\Account\Models\Account;
use Modules\Account\Services\AccountService;

class AccountController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
    ){
    }

    /**
     * Find all paginated accounts.
     */
    public function index(Request $request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);
        return $this->accountService->findAllAndPaginate($perPage);
    }

    /**
     * Store an account.
     *
     * @param  AccountRequest  $request
     * @return Account
     * @throws ResourceNotCreatedException
     */
    public function store(AccountRequest $request): Account
    {
        return $this->accountService->create($request->getDTO());
    }

    /**
     * Find an account.
     *
     * @param  int  $id
     * @return Account
     * @throws ResourceNotFoundException
     */
    public function show(int $id): Account
    {
        return $this->accountService->find($id);
    }

    /**
     * Update an account.
     *
     * @param  AccountRequest  $request
     * @param  int  $id
     * @return Account
     * @throws ResourceNotFoundException
     * @throws ResourceNotUpdatedException
     */
    public function update(AccountRequest $request, int $id): Account
    {
        return $this->accountService->update($id, $request->getDTO());
    }

    /**
     * Destroy an account
     *
     * @throws ResourceNotFoundException
     */
    public function destroy(int $id): Response
    {
        $this->accountService->delete($id);
        return response()->noContent();
    }
}
