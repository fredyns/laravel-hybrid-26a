<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Roles\RoleController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::post('layout-mode', function () {
    $mode = request('mode') === 'header' ? 'header' : 'sidebar';

    return back()->withCookie(cookie()->forever('layout_mode', $mode));
})->middleware('auth')->name('layout-mode.toggle');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::controller(UserController::class)->group(function () {
        Route::get('users', 'index')->name('users.index');
        Route::get('users/{user}', 'show')->name('users.show');
    });

    Route::controller(RoleController::class)->group(function () {
        Route::get('roles', 'index')->name('roles.index');
        Route::get('roles/{role}', 'show')->name('roles.show');
    });

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/s3.php';
