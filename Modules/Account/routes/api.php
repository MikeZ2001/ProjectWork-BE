<?php

use Illuminate\Support\Facades\Route;
use Modules\Account\Http\Controllers\AccountController;
use Modules\OAuth\Http\Middleware\AuthCookieMiddleware;

Route::middleware(['auth:api'])->middleware(AuthCookieMiddleware::class)->prefix('v1')->group(function () {
    Route::get('accounts', [AccountController::class, 'index']);
});
