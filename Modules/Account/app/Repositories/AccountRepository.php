<?php

namespace Modules\Account\Repositories;

use App\Repositories\EloquentRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Account\Models\Account;


class AccountRepository extends EloquentRepository
{
    public function __construct()
    {
        return $this->modelClass = Account::class;
    }

    /**
     * Find all accounts for logged user and paginate results.
     *
     * @param  int  $perPage
     * @return LengthAwarePaginator
     */
    public function findAllAndPaginateForUser(int $perPage = 10): LengthAwarePaginator
    {
        return $this->makeBuilder()
            ->where('user_id', '=', Auth::id())
            ->paginate(perPage: $perPage);
    }
}