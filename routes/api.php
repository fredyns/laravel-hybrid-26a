<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Settings\DeleteAccountController;
use App\Http\Controllers\Api\Settings\UpdatePasswordController;
use App\Http\Controllers\Api\Settings\UpdateProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('register', RegisterController::class)->name('api.auth.register');
    Route::post('login', LoginController::class)->name('api.auth.login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('logout', LogoutController::class)->name('api.auth.logout');
    });
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('user', MeController::class)->name('api.user');

    Route::prefix('settings')->group(function (): void {
        Route::put('profile', UpdateProfileController::class)->name('api.settings.profile');
        Route::put('password', UpdatePasswordController::class)->name('api.settings.password');
        Route::delete('account', DeleteAccountController::class)->name('api.settings.account');
    });
});
