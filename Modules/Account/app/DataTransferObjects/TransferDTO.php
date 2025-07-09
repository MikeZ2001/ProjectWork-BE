<?php

namespace Modules\Account\DataTransferObjects;

use App\DataTransferObjects\EntityDTO;
use Modules\Account\Models\Transfer;

/**
 * @method Transfer hydrateModel(Transfer $transfer)
 */
readonly class TransferDTO extends EntityDTO
{
    public function __construct(
        protected int $from_account_id,
        protected int $to_account_id,
        protected int $amount,
        protected ?string $description = null,
    )  {
    }

    public function getFromAccountId(): int
    {
        return $this->from_account_id;
    }

    public function getToAccountId(): int
    {
        return $this->to_account_id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Transfer
     */
    public function toModel(): Transfer
    {
        return $this->hydrateModel(new Transfer());
    }
}