<?php

namespace Modules\Account\DataTransferObjects;

use App\DataTransferObjects\EntityDTO;
use Modules\Account\Models\Account;

/**
 * @method Account hydrateModel(Account $account)
 */
readonly class AccountDTO extends EntityDTO
{
    public function __construct(
        protected string $type,
        protected float $balance,
        protected string $open_date,
        protected string $close_date,
        protected string $status,
    ){
    }

    public function toModel(): Account
    {
        /** @var Account */
        return $this->hydrateModel(new Account());
    }
}