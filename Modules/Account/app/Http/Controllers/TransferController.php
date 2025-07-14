<?php

namespace Modules\Account\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ResourceNotUpdatedException;
use App\Http\Controllers\Controller;
use Modules\Account\Http\Requests\TransferRequest;
use Modules\Account\Services\TransferService;

/**
 * @group Modules
 * @subgroup Transfer
 */
class TransferController extends Controller
{
    public function __construct(private readonly TransferService $transferService)
    {
    }

    /**
     * Transfer funds from one account to another
     *
     * @param TransferRequest $transferRequest
     *
     * @bodyParam from_account_id integer required ID of the account to transfer **from**. Example: 1
     * @bodyParam to_account_id   integer required ID of the account to transfer **to**. Must differ from `from_account_id`. Example: 2
     * @bodyParam amount          number  required Amount to transfer (must be > 0, up to two decimals). Example: 100.00
     * @bodyParam description     string|null Optional description for the transfer. Example: Transfer to savings
     *
     * @response 204
     * @responseFile 422 storage/responses/transfers/validation-error.json
     * @responseFile 404 storage/responses/transfers/not-found.json
     * @responseFile 500 storage/responses/transfers/transfer-error.json
     *
     * @throws ResourceNotFoundException
     * @throws ResourceNotUpdatedException
     */
    public function transferFunds(TransferRequest $transferRequest): void
    {
        $this->transferService->transferFunds($transferRequest->getDTO());
    }
}