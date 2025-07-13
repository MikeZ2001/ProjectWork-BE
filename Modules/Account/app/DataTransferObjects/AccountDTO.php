<?php

namespace Modules\Account\DataTransferObjects;

use App\DataTransferObjects\EntityDTO;
use Brick\Math\BigDecimal;
use Modules\Account\Models\Account;

/**
 * @method Account hydrateModel(Account $account)
 */
readonly class AccountDTO extends EntityDTO
{
    public function __construct(
        protected string $name,
        protected string $type,
        protected string $balance,
        protected string $open_date,
        protected string $status,
        protected ?string $close_date = null,
    ){
    }

    /**
     * @return Account
     */
    public function toModel(): Account
    {
        /** @var Account */
        return $this->hydrateModel(new Account());
    }
}