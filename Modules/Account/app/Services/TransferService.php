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


        $transfer = $transferDTO->toModel();
        $transfer->user_id = Auth::id();

        try {
            DB::transaction(function () use ($transfer, $fromAccount, $toAccount, $transferDTO) {
                $this->transferRepository->create($transfer);

                $transactionWithdrawalDTO = new TransactionDTO(
                    amount: $transferDTO->getAmount(),
                    type: TransactionType::Withdrawal->value,
                    date: now()->toDateTimeString(),
                    transfer_id: $transfer->id
                );
                $transactionDepositDTO = new TransactionDTO(
                    amount: $transferDTO->getAmount(),
                    type: TransactionType::Deposit->value,
                    date: now()->toDateTimeString(),
                    transfer_id: $transfer->id
                );
                $fromAccount->balance -= $transferDTO->getAmount();

                $this->transactionService->create($fromAccount->id, $transactionWithdrawalDTO);
                $fromAccount->save();
                $toAccount->balance += $transferDTO->getAmount();
                $this->transactionService->create($toAccount->id, $transactionDepositDTO);
                $toAccount->save();
            });
        } catch (\Exception $ex) {
            dump($ex->getMessage());
            throw new ResourceNotUpdatedException('Amount could not be transfered.', previous: $ex);
        }
    }

    /**
     * @param int $transferId
     * @return void
     */
    public function deleteTransfer(int $transferId)
    {
        $transfer = $this->transferRepository->findById($transferId);
        $txs      = $transfer->transactions;

        DB::transaction(function() use($txs, $transfer) {

            foreach ($txs as $tx) {
                $this->transactionService->delete($tx->id);
            }

            $this->transferRepository->delete($transfer);
        });
    }
}