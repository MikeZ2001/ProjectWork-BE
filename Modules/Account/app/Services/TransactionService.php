<?php

namespace Modules\Account\Services;

use App\Exceptions\ResourceNotCreatedException;
use App\Exceptions\ResourceNotDeletedException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ResourceNotUpdatedException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Account\DataTransferObjects\AccountDTO;
use Modules\Account\DataTransferObjects\TransactionDTO;
use Modules\Account\Models\Account;
use Modules\Account\Models\Transaction;
use Modules\Account\Models\TransactionType;
use Modules\Account\Repositories\TransactionRepository;
use Throwable;

readonly class TransactionService
{
    public function __construct(
        private AccountService $accountService,
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
     * @throws ResourceNotFoundException|ResourceNotCreatedException|Throwable
     */
    public function create(int $accountId, TransactionDTO $transactionDTO): Transaction
    {
        $account = $this->accountService->find($accountId);

        $transaction = $transactionDTO->toModel();
        $transaction->user_id = Auth::id();
        $transaction->account_id = $accountId;
        return DB::transaction(function () use ($accountId, $transaction, $account) {
            try {
                $transaction = $this->transactionRepository->create($transaction);
                $this->manageTransaction($account, $transaction);
                return $transaction;
            } catch (Throwable $ex) {
                throw new ResourceNotCreatedException("Transaction could not be created.", previous: $ex);
            }
        });

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
        $account = $this->accountService->find($transaction->account()->first()->id);
        $transactionDTO->hydrateModel($transaction);
        return DB::transaction(function () use ($account, $transaction, $transactionDTO) {
            try {
                $transaction = $this->transactionRepository->update($transaction);
                $this->manageTransaction($account, $transaction);
                return $transaction;
            } catch (Throwable $ex) {
                throw new ResourceNotUpdatedException("Transaction could not be updated.", previous: $ex);
            }
        });
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
        $account = $this->accountService->find($transaction->account()->first()->id);
        DB::transaction(function () use ($account, $transaction) {
            try {
                $this->transactionRepository->delete($transaction);
                $this->managetransactionReverse($account, $transaction);
            } catch (Throwable $ex) {
                throw new ResourceNotDeletedException("Transaction could not be deleted.", previous: $ex);
            }
        });

    }

    /**
     * Helper method to manage transaction and account balance update.
     *
     * @param  Account  $account
     * @param  Transaction  $transaction
     * @return void
     * @throws ResourceNotFoundException
     * @throws ResourceNotUpdatedException
     */
    private function manageTransaction(Account $account, Transaction $transaction)
    {
        if ($transaction->type === TransactionType::Deposit) {
            $account->balance += $transaction->amount;
        } elseif ($transaction->type === TransactionType::Withdrawal) {
            $account->balance -= $transaction->amount;
        }

        $accountDTO = new AccountDTO(
            type: $account->type->value,
            balance: $account->balance,
            open_date: $account->open_date,
            status:  $account->status->value,
            close_date: $account->close_date
        );

        $this->accountService->update($account->id, $accountDTO);
    }

    private function managetransactionReverse(Account $account, Transaction $transaction)
    {
        if ($transaction->type === TransactionType::Deposit) {
            $account->balance -= $transaction->amount;
        } elseif ($transaction->type === TransactionType::Withdrawal) {
            $account->balance += $transaction->amount;
        }

        $accountDTO = new AccountDTO(
            type: $account->type->value,
            balance: $account->balance,
            open_date: $account->open_date,
            status:  $account->status->value,
            close_date: $account->close_date
        );

        $this->accountService->update($account->id, $accountDTO);
    }
}