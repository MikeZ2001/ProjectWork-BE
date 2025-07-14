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
        protected string $amount,
        protected string $type,
        protected string $transaction_date,
        protected ?int $category_id = null,
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