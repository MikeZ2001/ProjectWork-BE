<?php

namespace Modules\Account\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ResourceNotUpdatedException;
use App\Http\Controllers\Controller;
use Modules\Account\Http\Requests\TransferRequest;
use Modules\Account\Services\TransferService;

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
     * @throws ResourceNotFoundException
     * @throws ResourceNotUpdatedException
     */
    public function transferFunds(TransferRequest $transferRequest): void
    {
        $this->transferService->transferFunds($transferRequest->getDTO());
    }
}