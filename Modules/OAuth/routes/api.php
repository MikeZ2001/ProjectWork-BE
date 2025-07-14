<?php

use Illuminate\Support\Facades\Route;
use Modules\OAuth\Http\Controllers\OAuthController;
use Modules\OAuth\Http\Middleware\AuthCookieMiddleware;

// Public routes
Route::post('login', [OAuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:api'])->middleware(AuthCookieMiddleware::class)->group(function () {
    Route::post('logout', [OAuthController::class, 'logout']);
    Route::get('user', [OAuthController::class, 'user']);
});