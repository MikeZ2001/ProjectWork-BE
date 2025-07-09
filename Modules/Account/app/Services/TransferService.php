<?php

namespace Modules\Account\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ResourceNotUpdatedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Account\DataTransferObjects\TransactionDTO;
use Modules\Account\DataTransferObjects\TransferDTO;
use Modules\Account\Models\TransactionType;
use Modules\Account\Repositories\TransferRepository;
use Throwable;

readonly class TransferService
{

    public function __construct(
        private AccountService $accountService,
        private TransactionService $transactionService,
        private TransferRepository $transferRepository
    ) {
    }

    /**
     * Transfer funds from one account to another
     *
     * @throws ResourceNotFoundException
     * @throws ResourceNotUpdatedException
     */
    public function transferFunds(TransferDTO $transferDTO): void
    {
        $fromAccount = $this->accountService->find($transferDTO->getFromAccountId());
        $toAccount = $this->accountService->find($transferDTO->getToAccountId());

        if ($fromAccount->balance < $transferDTO->getAmount()) {
            throw new ResourceNotUpdatedException('You do not have sufficient balance.');
        }
        $transactionDTO = new TransactionDTO(
            amount: $transferDTO->getAmount(),
            type: TransactionType::Transfer->value,
            date: now()->toDateTimeString(),
        );

        $transfer = $transferDTO->toModel();
        $transfer->user_id = Auth::id();

        try {
            DB::transaction(function () use ($transfer, $fromAccount, $toAccount, $transferDTO, $transactionDTO) {
                $fromAccount->balance -= $transferDTO->getAmount();

                $this->transactionService->create($fromAccount->id, $transactionDTO);
                $fromAccount->save();
                $toAccount->balance += $transferDTO->getAmount();
                $this->transactionService->create($toAccount->id, $transactionDTO);
                $toAccount->save();

                $this->transferRepository->create($transfer);
            });
        } catch (Throwable $ex) {
            dump($ex->getMessage());
            throw new ResourceNotUpdatedException('Amount could not be transfered.', previous: $ex);
        }
    }
}