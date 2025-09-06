<?php

use Illuminate\Support\Facades\Route;
use Modules\OAuth\Http\Controllers\OAuthController;
use Modules\OAuth\Http\Middleware\AuthCookieMiddleware;

// Public routes
Route::post('login', [OAuthController::class, 'login']);
Route::post('login-manual', [OAuthController::class, 'loginManual']);

// Protected routes
Route::middleware([AuthCookieMiddleware::class, 'auth:api'])->group(function () {
    Route::post('logout', [OAuthController::class, 'logout']);
    Route::get('user', [OAuthController::class, 'user']);
});

// Debug routes (remove in production)
Route::middleware([AuthCookieMiddleware::class])->group(function () {
    Route::get('debug', [OAuthController::class, 'debug']);
    Route::get('test-auth', [OAuthController::class, 'testAuth']);
    Route::get('test-user', [OAuthController::class, 'testUser']);
});