<?php

use Illuminate\Support\Facades\Route;
use Modules\Account\Http\Controllers\AccountController;
use Modules\Account\Http\Controllers\CategoryController;
use Modules\Account\Http\Controllers\TransactionController;
use Modules\Account\Http\Controllers\TransferController;
use Modules\OAuth\Http\Middleware\AuthCookieMiddleware;

Route::middleware([AuthCookieMiddleware::class, 'auth:api'])->prefix('v1')->group(function () {
    Route::apiResource('accounts', AccountController::class);

    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('accounts.transactions', TransactionController::class)
        ->shallow();

    Route::get('transactions-by-user', [TransactionController::class, 'findAllByAuthUser']);

    Route::post('transfers', [TransferController::class, 'transferFunds']);
});
