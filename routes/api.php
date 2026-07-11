<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Notifications\DestroyController as NotificationDestroyController;
use App\Http\Controllers\Api\Notifications\IndexController as NotificationIndexController;
use App\Http\Controllers\Api\Notifications\MarkAllReadController;
use App\Http\Controllers\Api\Notifications\MarkReadController;
use App\Http\Controllers\Api\Roles\DestroyController as RoleDestroyController;
use App\Http\Controllers\Api\Roles\IndexController as RoleIndexController;
use App\Http\Controllers\Api\Roles\PermissionIndexController;
use App\Http\Controllers\Api\Roles\ShowController as RoleShowController;
use App\Http\Controllers\Api\Roles\StoreController as RoleStoreController;
use App\Http\Controllers\Api\Roles\UpdateController as RoleUpdateController;
use App\Http\Controllers\Api\Settings\DeleteAccountController;
use App\Http\Controllers\Api\Settings\UpdatePasswordController;
use App\Http\Controllers\Api\Settings\UpdateProfileController;
use App\Http\Controllers\Api\Users\ChangePasswordController;
use App\Http\Controllers\Api\Users\DestroyController;
use App\Http\Controllers\Api\Users\IndexController;
use App\Http\Controllers\Api\Users\ShowController;
use App\Http\Controllers\Api\Users\StoreController;
use App\Http\Controllers\Api\Users\UpdateController;
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

    Route::prefix('roles')->group(function (): void {
        Route::get('/', RoleIndexController::class)
            ->middleware('can:rbac.index')
            ->name('api.roles.index');

        Route::post('/', RoleStoreController::class)
            ->middleware('can:rbac.create')
            ->name('api.roles.store');

        Route::get('{role}', RoleShowController::class)
            ->middleware('can:rbac.show')
            ->name('api.roles.show');

        Route::put('{role}', RoleUpdateController::class)
            ->middleware('can:rbac.update')
            ->name('api.roles.update');

        Route::delete('{role}', RoleDestroyController::class)
            ->middleware('can:rbac.delete')
            ->name('api.roles.destroy');
    });

    Route::get('permissions', PermissionIndexController::class)
        ->middleware('can:rbac.index')
        ->name('api.permissions.index');

    Route::prefix('notifications')->group(function (): void {
        Route::get('/', NotificationIndexController::class)->name('api.notifications.index');
        Route::post('read-all', MarkAllReadController::class)->name('api.notifications.read-all');
        Route::put('{notificationId}/read', MarkReadController::class)->name('api.notifications.read');
        Route::delete('{notificationId}', NotificationDestroyController::class)->name('api.notifications.destroy');
    });

    Route::prefix('settings')->group(function (): void {
        Route::put('profile', UpdateProfileController::class)->name('api.settings.profile');
        Route::put('password', UpdatePasswordController::class)->name('api.settings.password');
        Route::delete('account', DeleteAccountController::class)->name('api.settings.account');
    });
});
