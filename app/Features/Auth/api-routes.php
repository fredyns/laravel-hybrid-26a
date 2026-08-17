<?php

use App\Features\Auth\Login\LoginApiController;
use App\Features\Auth\LogoutApiController;
use App\Features\Auth\MeApiController;
use App\Features\Auth\Register\RegisterApiController;

Route::prefix('auth')->group(function (): void {
    Route::post('register', RegisterApiController::class)->name('api.auth.register');
    Route::post('login', LoginApiController::class)->name('api.auth.login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('logout', LogoutApiController::class)->name('api.auth.logout');
    });
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('user', MeApiController::class)->name('api.user');
});
