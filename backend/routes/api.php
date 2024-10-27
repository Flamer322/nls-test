<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Models\Task;
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

Route::controller(TaskController::class)
    ->prefix('tasks')
    ->name('tasks.')
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/{page}', 'getPaginated')
            ->name('get_paginated')
            ->can('viewAny', Task::class);

        Route::post('/', 'create')
            ->name('create')
            ->can('create', Task::class);

        Route::patch('/{task}', 'update')
            ->name('update')
            ->can('update', 'task');

        Route::delete('/{task}', 'delete')
            ->name('delete')
            ->can('delete', 'task');
    });
