<?php

use App\Livewire\Pages\Notifications\Index as NotificationsIndex;
use App\Livewire\Pages\Roles\Index as RolesIndex;
use App\Livewire\Pages\Roles\Show as RolesShow;
use App\Livewire\Pages\Users\Index as UsersIndex;
use App\Livewire\Pages\Users\Show as UsersShow;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::post('layout-mode', function () {
    $mode = request('mode') === 'header' ? 'header' : 'sidebar';

    return back()->withCookie(cookie()->forever('layout_mode', $mode));
})->middleware('auth')->name('layout-mode.toggle');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('users', UsersIndex::class)
        ->middleware('can:users.index')
        ->name('users.index');

    Route::livewire('users/{user}', UsersShow::class)
        ->middleware('can:users.show')
        ->name('users.show');

    Route::livewire('roles', RolesIndex::class)
        ->middleware('can:rbac.index')
        ->name('roles.index');

    Route::livewire('roles/{role}', RolesShow::class)
        ->middleware('can:rbac.show')
        ->name('roles.show');

    Route::livewire('notifications', NotificationsIndex::class)
        ->name('notifications.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/s3.php';
