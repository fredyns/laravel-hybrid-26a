<?php

use App\Http\Controllers\MyAccessController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Roles\RoleCreateController;
use App\Http\Controllers\Roles\RoleEditController;
use App\Http\Controllers\Roles\RoleIndexController;
use App\Http\Controllers\Roles\RoleShowController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::post('layout-mode', function () {
    $mode = request('mode') === 'header' ? 'header' : 'sidebar';

    return back()->withCookie(cookie()->forever('layout_mode', $mode));
})->middleware('auth')->name('layout-mode.toggle');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('my-access', MyAccessController::class)->name('my-access');

    Route::get('roles', RoleIndexController::class)->name('roles.index');
    Route::get('roles/create', RoleCreateController::class)->name('roles.create');
    Route::get('roles/{role}', RoleShowController::class)->name('roles.show');
    Route::get('roles/{role}/edit', RoleEditController::class)->name('roles.edit');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/s3.php';
