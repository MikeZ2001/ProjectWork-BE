<?php

use Illuminate\Support\Facades\Route;
use Modules\Account\Http\Controllers\AccountController;
use Modules\Account\Http\Controllers\TransactionController;
use Modules\Account\Http\Controllers\TransferController;
use Modules\OAuth\Http\Middleware\AuthCookieMiddleware;

Route::middleware(['auth:api'])->middleware(AuthCookieMiddleware::class)->prefix('v1')->group(function () {
    Route::apiResource('accounts', AccountController::class);

    Route::apiResource('accounts.transactions', TransactionController::class)
        ->shallow();

    Route::post('transfers', [TransferController::class, 'transferFunds']);
});
