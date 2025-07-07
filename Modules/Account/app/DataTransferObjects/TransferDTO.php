<?php

namespace Modules\Account\DataTransferObjects;

use App\DataTransferObjects\DTO;

/**
 * @method getDTO()
 */
readonly class TransferDTO extends DTO
{
    public function __construct(
        private int $from_account_id,
        private int $to_account_id,
        private int $amount,
        private ?string $description = null,
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
}