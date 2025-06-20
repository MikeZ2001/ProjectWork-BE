<?php

namespace Modules\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Account\Http\Requests\AccountRequest;
use Modules\Account\Models\Account;
use Modules\Account\Services\AccountService;
use Modules\User\Models\User;

class AccountController extends Controller
{

    public function __construct(
        private AccountService $accountService,
    ){
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Account::all();
    }

    public function store(AccountRequest $request): Account
    {
        return $this->accountService->create($request->getDTO());
    }
}
