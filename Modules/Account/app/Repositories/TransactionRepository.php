<?php

namespace Modules\Account\Repositories;

use App\Repositories\EloquentRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Account\Models\Transaction;

/**
 * @method findById(int $id, array $relationList = null)
 * @method create(Transaction $transaction)
 * @method update(Transaction $transaction)
 * @method delete(Transaction $transaction)
 */
class TransactionRepository extends EloquentRepository
{
    protected string $modelClass = Transaction::class;

    protected array $baseRelationList = ['category'];

    /**
     * Find all transactions by logged user and account and paginate results.
     *
     * @param int $accountId
     * @param  int  $perPage
     * @return LengthAwarePaginator
     */
    public function findAllAndPaginateForUserAndAccount(int $accountId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->makeBuilder()
            ->where('user_id', '=', Auth::id())
            ->where('account_id', '=', $accountId)
            ->paginate(perPage: $perPage);
    }
}