<?php

namespace Modules\Account\DataTransferObjects;

use App\DataTransferObjects\EntityDTO;
use Modules\Account\Models\Transaction;

/**
 * @method Transaction hydrateModel(Transaction $transaction)
 */
readonly class TransactionDTO extends EntityDTO
{
    public function __construct(
        protected float $amount,
        protected string $type,
        protected string $date,
        protected ?string $description = null,
        protected ?int $transfer_id = null,
    ){
    }

    /**
     * @return Transaction
     */
    public function toModel(): Transaction
    {
        /** @var Transaction */
        return $this->hydrateModel(new Transaction());
    }
}