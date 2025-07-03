<?php

namespace Modules\Account\Http\Controllers;

use App\Exceptions\ResourceNotCreatedException;
use App\Exceptions\ResourceNotDeletedException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ResourceNotUpdatedException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Account\Http\Requests\TransactionRequest;
use Modules\Account\Models\Transaction;
use Modules\Account\Services\TransactionService;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $accountService,
    ){
    }

    /**
     * Find all paginated accounts.
     */
    public function index(Request $request, int $accountId): LengthAwarePaginator
    {
        $perPage = $request->integer('per_page', 10);
        return $this->accountService->findAllAndPaginateForUserAndAccount(accountId: $accountId, perPage: $perPage);
    }

    /**
     * Store an account.
     *
     * @param  TransactionRequest  $request
     * @return Transaction
     * @throws ResourceNotCreatedException
     */
    public function store(TransactionRequest $request, int $accountId): Transaction
    {
        return $this->accountService->create($accountId, $request->getDTO());
    }

    /**
     * Find an account.
     *
     * @param  int  $id
     * @return Transaction
     * @throws ResourceNotFoundException
     */
    public function show(int $id): Transaction
    {
        return $this->accountService->find($id);
    }

    /**
     * Update an account.
     *
     * @param  TransactionRequest  $request
     * @param  int  $id
     * @return Transaction
     * @throws ResourceNotFoundException
     * @throws ResourceNotUpdatedException
     */
    public function update(int $id, TransactionRequest $request): Transaction
    {
        return $this->accountService->update($id, $request->getDTO());
    }

    /**
     * Destroy an account
     *
     * @throws ResourceNotFoundException
     * @throws ResourceNotDeletedException
     */
    public function destroy(int $id): Response
    {
        $this->accountService->delete($id);
        return response()->noContent();
    }
}
