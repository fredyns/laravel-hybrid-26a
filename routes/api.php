<?php

use App\Http\Controllers\Api\Settings\DeleteAccountController;
use App\Http\Controllers\Api\Settings\UpdatePasswordController;
use App\Http\Controllers\Api\Settings\UpdateProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('user', MeController::class)->name('api.user');

    Route::prefix('settings')->group(function (): void {
        Route::put('profile', UpdateProfileController::class)->name('api.settings.profile');
        Route::put('password', UpdatePasswordController::class)->name('api.settings.password');
        Route::delete('account', DeleteAccountController::class)->name('api.settings.account');
    });
});
