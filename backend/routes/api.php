<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
    ->prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::post('register', 'register')
            ->name('register');

        Route::post('login', 'login')
            ->name('login');

        Route::post('logout', 'logout')
            ->name('logout')
            ->middleware('auth:api');

        Route::post('me', 'me')
            ->name('me')
            ->middleware('auth:api');
    });
