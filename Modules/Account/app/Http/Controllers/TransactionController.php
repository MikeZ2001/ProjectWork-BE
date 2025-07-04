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
use Throwable;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ){
    }

    /**
     * Find and paginates all transactions for logged user and for selected account.
     */
    public function index(Request $request, int $accountId): LengthAwarePaginator
    {
        $perPage = $request->integer('per_page', 10);
        return $this->transactionService->findAllAndPaginateForUserAndAccount(accountId: $accountId, perPage: $perPage);
    }

    /**
     * Store a transaction for an account.
     *
     * @param TransactionRequest  $request
     * @param int $accountId
     * @return Transaction
     * @throws ResourceNotCreatedException|ResourceNotFoundException|Throwable
     */
    public function store(TransactionRequest $request, int $accountId): Transaction
    {
        return $this->transactionService->create($accountId, $request->getDTO());
    }

    /**
     * Find a transaction.
     *
     * @param  int  $id
     * @return Transaction
     * @throws ResourceNotFoundException
     */
    public function show(int $id): Transaction
    {
        return $this->transactionService->find($id);
    }

    /**
     * Update a transaction.
     *
     * @param  TransactionRequest  $request
     * @param  int  $id
     * @return Transaction
     * @throws ResourceNotFoundException
     * @throws ResourceNotUpdatedException
     */
    public function update(int $id, TransactionRequest $request): Transaction
    {
        return $this->transactionService->update($id, $request->getDTO());
    }

    /**
     * Destroy a transaction.
     *
     * @throws ResourceNotFoundException
     * @throws ResourceNotDeletedException
     */
    public function destroy(int $id): Response
    {
        $this->transactionService->delete($id);
        return response()->noContent();
    }
}
