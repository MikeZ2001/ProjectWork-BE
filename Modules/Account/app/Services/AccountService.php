<?php

namespace Modules\Account\Services;

use App\Exceptions\ResourceNotCreatedException;
use App\Exceptions\ResourceNotDeletedException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ResourceNotUpdatedException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Account\DataTransferObjects\AccountDTO;
use Modules\Account\Models\Account;
use Modules\Account\Repositories\AccountRepository;
use Throwable;

readonly class AccountService
{
    public function __construct(
        private AccountRepository $accountRepository,
    ) {
    }

    /**
     * Find and paginates all account for logged user.
     *
     * @param  int  $perPage
     * @return LengthAwarePaginator
     */
    public function findAllAndPaginateForUser(int $perPage): LengthAwarePaginator
    {
        return $this->accountRepository->findAllAndPaginateForUser(perPage: $perPage);
    }

    /**
     * Create an account.
     *
     * @param  AccountDTO  $accountDTO
     * @return Account
     * @throws ResourceNotCreatedException
     */
    public function create(AccountDTO $accountDTO): Account
    {
        $account = $accountDTO->toModel();
        $account->user_id = Auth::id();
        try {
            return $this->accountRepository->create($account);
        } catch (Throwable $ex) {
            dump($ex->getMessage());
            throw new ResourceNotCreatedException("Account could not be created.", previous: $ex);
        }
    }

    /**
     * Find an account.
     *
     * @throws ResourceNotFoundException
     */
    public function find(int $id): Account
    {
        try {
            return $this->accountRepository->findById($id);
        } catch (Throwable $ex) {
            throw new ResourceNotFoundException("Account could not be found.", previous: $ex);
        }
    }

    /**
     * Update an account.
     *
     * @param  int  $id
     * @param  AccountDTO  $accountDTO
     * @return Account
     * @throws ResourceNotFoundException
     * @throws ResourceNotUpdatedException
     */
    public function update(int $id, AccountDTO $accountDTO): Account
    {
        $account = $this->find($id);

        try {
            $accountDTO->hydrateModel($account);
            return $this->accountRepository->update($account);
        } catch (Throwable $ex) {
            throw new ResourceNotUpdatedException("Account could not be updated.", previous: $ex);
        }
    }


    /**
     * Delete an account.
     *
     * @param  int  $id
     * @return void
     * @throws ResourceNotDeletedException
     * @throws ResourceNotFoundException
     */
    public function delete(int $id): void
    {
        $account = $this->find($id);
        try {
            $this->accountRepository->delete($account);
        } catch (Throwable $ex) {
            throw new ResourceNotDeletedException("Account could not be deleted.", previous: $ex);
        }
    }
}