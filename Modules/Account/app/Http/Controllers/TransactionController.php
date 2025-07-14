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
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Account\Http\Requests\TransactionRequest;
use Modules\Account\Http\Requests\TransferRequest;
use Modules\Account\Http\Resources\TransactionResource;
use Modules\Account\Models\Transaction;
use Modules\Account\Services\TransactionService;
use Throwable;

/**
 * @group Modules
 * @subgroup Transaction
 */
class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ){
    }

    /**
     * Find and paginates all transactions for logged user and for selected account.
     *
     * @param  Request  $request
     * @param  int  $accountId
     * @return AnonymousResourceCollection
     *
     * @urlParam account_id integer required The ID of the account. Example: 1
     * @queryParam per_page integer The number of items per page. Default: 10. Example: 15
     *
     * @responseFile 200 responses/transactions/index-success.json
     */
    public function index(Request $request, int $accountId): AnonymousResourceCollection
    {
        $perPage = $request->integer('per_page', 10);
        return TransactionResource::collection($this->transactionService->findAllAndPaginateForUserAndAccount(accountId: $accountId, perPage: $perPage));
    }

    /**
     * Store a transaction for an account.
     *
     * @param TransactionRequest  $request
     * @param int $accountId
     * @return TransactionResource
     * @throws ResourceNotCreatedException|ResourceNotFoundException|Throwable
     *
     * @urlParam account_id integer required The ID of the account. Example: 1
     *
     * @bodyParam type string required Transaction type (Deposit, Withdrawal or Transfer). Example: Withdrawal
     * @bodyParam amount number required Transaction amount (must be > 0, up to two decimals). Example: 100.00
     * @bodyParam transaction_date string required Date of transaction (YYYY-MM-DD). Example: 2025-07-14
     * @bodyParam description string|null Optional description. Example: Other
     * @bodyParam category_id integer required Category identifier. Example: 42
     *
     * @responseFile 201 storage/responses/transactions/create-success.json
     * @responseFile 422 storage/responses/transactions/validation-error.json
     * @responseFile 500 storage/responses/transactions/create-error.json
    */
    public function store(TransactionRequest $request, int $accountId): TransactionResource
    {
        return new TransactionResource($this->transactionService->create($accountId, $request->getDTO()));
    }

    /**
     * Find a transaction.
     *
     * @param  int  $id
     * @return TransactionResource
     * @throws ResourceNotFoundException
     *
     * @urlParam id integer required Transaction ID. Example: 1
     *
     * @responseFile 200 storage/responses/transactions/show-success.json
     * @responseFile 404 storage/responses/transactions/not-found.json
     */
    public function show(int $id): TransactionResource
    {
        return new TransactionResource($this->transactionService->find($id));
    }

    /**
     * Update a transaction.
     *
     * @param  TransactionRequest  $request
     * @param  int  $id
     * @return TransactionResource
     * @throws ResourceNotFoundException
     * @throws ResourceNotUpdatedException
     *
     * @urlParam id integer required The ID of the transaction. Example: 1
     *
     * @bodyParam type string required Transaction type (Deposit, Withdrawal or Transfer). Example: Deposit
     * @bodyParam amount number required Transaction amount (must be > 0, up to two decimals). Example: 150.00
     * @bodyParam transaction_date string required Date of transaction (YYYY-MM-DD). Example: 2025-07-15
     * @bodyParam description string|null Optional description. Example: Updated description
     * @bodyParam category_id integer required Category identifier. Example: 42
     *
     * @responseFile 200 storage/responses/transactions/update-success.json
     * @responseFile 422 storage/responses/transactions/validation-error.json
     * @responseFile 404 storage/responses/transactions/not-found.json
     * @responseFile 500 storage/responses/transactions/update-error.json
     */
    public function update(int $id, TransactionRequest $request): TransactionResource
    {
        return new TransactionResource($this->transactionService->update($id, $request->getDTO()));
    }

    /**
     * Destroy a transaction.
     *
     * @throws ResourceNotFoundException
     * @throws ResourceNotDeletedException
     *
     * @response 204
     * @responseFile 404 storage/responses/transactions/not-found.json
     * @responseFile 500 storage/responses/transactions/delete-error.json
     *
     * @urlParam id integer required The ID of the transaction. Example: 1
     */
    public function destroy(int $id): Response
    {
        $this->transactionService->delete($id);
        return response()->noContent();
    }
}
