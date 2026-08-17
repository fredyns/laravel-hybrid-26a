<?php

use App\Features\Settings\DeleteAccount\DeleteAccountApiController;
use App\Features\Settings\UpdatePassword\UpdatePasswordApiController;
use App\Features\Settings\UpdateProfile\UpdateProfileApiController;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::prefix('settings')->group(function (): void {
        Route::put('profile', UpdateProfileApiController::class)->name('api.settings.profile');
        Route::put('password', UpdatePasswordApiController::class)->name('api.settings.password');
        Route::delete('account', DeleteAccountApiController::class)->name('api.settings.account');
    });
});
