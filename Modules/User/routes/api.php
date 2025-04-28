<?php

use Illuminate\Support\Facades\Route;
use Modules\User\app\Http\Controllers\UserController;

Route::post('v1/register', [UserController::class, 'register']);