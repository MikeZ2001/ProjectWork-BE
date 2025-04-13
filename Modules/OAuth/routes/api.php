<?php

use Illuminate\Support\Facades\Route;
use Modules\OAuth\Http\Controllers\OAuthController;

// Public routes
    Route::post('register', [OAuthController::class, 'register']);
    Route::post('login', [OAuthController::class, 'login']);

// Protected routes
    Route::middleware('auth:api')->group(function () {
        // Auth routes
        Route::post('logout', [OAuthController::class, 'logout']);
        Route::get('user', [OAuthController::class, 'user']);
    });