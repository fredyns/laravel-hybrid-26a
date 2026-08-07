<?php

use App\Features\User\ChangePassword\ChangePasswordApiController;
use App\Features\User\Create\CreateUserApiController;
use App\Features\User\Delete\DeleteUserApiController;
use App\Features\User\Index\IndexUsersApiController;
use App\Features\User\Show\ShowUserApiController;
use App\Features\User\Update\UpdateUserApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('users')->group(function (): void {
    Route::get('/', IndexUsersApiController::class)
        ->middleware('can:users.index')
        ->name('api.users.index');

    Route::post('/', CreateUserApiController::class)
        ->middleware('can:users.create')
        ->name('api.users.store');

    Route::get('{user}', ShowUserApiController::class)
        ->middleware('can:users.show')
        ->name('api.users.show');

    Route::put('{user}', UpdateUserApiController::class)
        ->middleware('can:users.update')
        ->name('api.users.update');

    Route::delete('{user}', DeleteUserApiController::class)
        ->middleware('can:users.delete')
        ->name('api.users.destroy');

    Route::put('{user}/password', ChangePasswordApiController::class)
        ->middleware('can:users.change-password')
        ->name('api.users.change-password');
});
