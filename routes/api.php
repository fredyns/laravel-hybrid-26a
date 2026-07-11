<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Users\ChangePasswordController;
use App\Http\Controllers\Api\Users\DestroyController;
use App\Http\Controllers\Api\Users\IndexController;
use App\Http\Controllers\Api\Users\ShowController;
use App\Http\Controllers\Api\Users\StoreController;
use App\Http\Controllers\Api\Users\UpdateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('register', RegisterController::class)->name('api.auth.register');
    Route::post('login', LoginController::class)->name('api.auth.login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('logout', LogoutController::class)->name('api.auth.logout');
    });
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('user', fn (Request $request) => $request->user())->name('api.user');

    Route::prefix('users')->group(function (): void {
        Route::get('/', IndexController::class)
            ->middleware('can:users.index')
            ->name('api.users.index');

        Route::post('/', StoreController::class)
            ->middleware('can:users.create')
            ->name('api.users.store');

        Route::get('{user}', ShowController::class)
            ->middleware('can:users.show')
            ->name('api.users.show');

        Route::put('{user}', UpdateController::class)
            ->middleware('can:users.update')
            ->name('api.users.update');

        Route::delete('{user}', DestroyController::class)
            ->middleware('can:users.delete')
            ->name('api.users.destroy');

        Route::put('{user}/password', ChangePasswordController::class)
            ->middleware('can:users.change-password')
            ->name('api.users.change-password');
    });
});
