<?php

namespace Modules\Account\Services;

use App\Exceptions\ResourceNotCreatedException;
use App\Exceptions\ResourceNotDeletedException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ResourceNotUpdatedException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Account\DataTransferObjects\TransactionDTO;
use Modules\Account\Models\Transaction;
use Modules\Account\Repositories\TransactionRepository;
use Throwable;

readonly class TransactionService
{
    public function __construct(
        private TransactionRepository $transactionRepository,
    ) {
    }

    /**
     * Find and paginates all transactions for logged user and for selected account.
     *
     * @param int $accountId
     * @param  int  $perPage
     * @return LengthAwarePaginator
     */
    public function findAllAndPaginateForUserAndAccount(int $accountId, int $perPage): LengthAwarePaginator
    {
        return $this->transactionRepository->findAllAndPaginateForUserAndAccount(accountId: $accountId, perPage: $perPage);
    }

    /**
     * Create a transaction.
     *
     * @param int $accountId
     * @param  TransactionDTO  $transactionDTO
     * @return Transaction
     * @throws ResourceNotCreatedException
     */
    public function create(int $accountId, TransactionDTO $transactionDTO): Transaction
    {
        $transaction = $transactionDTO->toModel();
        $transaction->user_id = Auth::id();
        $transaction->account_id = $accountId;
        try {
            return $this->transactionRepository->create($transaction);
        } catch (Throwable $ex) {
            dump($ex->getMessage());
            throw new ResourceNotCreatedException("Transaction could not be created.", previous: $ex);
        }
    }

    /**
     * Find a transaction.
     *
     * @throws ResourceNotFoundException
     */
    public function find(int $id): Transaction
    {
        try {
            return $this->transactionRepository->findById($id);
        } catch (Throwable $ex) {
            throw new ResourceNotFoundException("Transaction could not be found.", previous: $ex);
        }
    }

    /**
     * Update a transaction.
     *
     * @param  int  $id
     * @param  TransactionDTO  $transactionDTO
     * @return Transaction
     * @throws ResourceNotFoundException
     * @throws ResourceNotUpdatedException
     */
    public function update(int $id, TransactionDTO $transactionDTO): Transaction
    {
        $transaction = $this->find($id);

        try {
            $transactionDTO->hydrateModel($transaction);
            return $this->transactionRepository->update($transaction);
        } catch (Throwable $ex) {
            throw new ResourceNotUpdatedException("Transaction could not be updated.", previous: $ex);
        }
    }


    /**
     * Delete a transaction.
     *
     * @param  int  $id
     * @return void
     * @throws ResourceNotDeletedException
     * @throws ResourceNotFoundException
     */
    public function delete(int $id): void
    {
        $transaction = $this->find($id);
        try {
            $this->transactionRepository->delete($transaction);
        } catch (Throwable $ex) {
            throw new ResourceNotDeletedException("Transaction could not be deleted.", previous: $ex);
        }
    }
}