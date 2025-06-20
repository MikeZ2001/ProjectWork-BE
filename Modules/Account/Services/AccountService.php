<?php

namespace Modules\Account\Services;

use Illuminate\Support\Facades\Auth;
use Modules\Account\DataTransferObjects\AccountDTO;
use Modules\Account\Models\Account;
use Modules\Account\Repositories\AccountRepository;

class AccountService
{
    public function __construct(
        private AccountRepository $accountRepository,
    ) {
    }

    public function create(AccountDTO $accountDTO): Account
    {
        //dd($accountDTO->toModel());
        $account = $accountDTO->toModel();
        $account->user_id = Auth::id();
        return $this->accountRepository->create($account);
    }
}