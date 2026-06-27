<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('users', 'pages::users.index')
        ->middleware('can:users.index')
        ->name('users.index');

    Route::livewire('users/{user}', 'pages::users.show')
        ->middleware('can:users.show')
        ->name('users.show');

    Route::livewire('roles', 'pages::roles.index')
        ->middleware('can:rbac.index')
        ->name('roles.index');

    Route::livewire('roles/{role}', 'pages::roles.show')
        ->middleware('can:rbac.show')
        ->name('roles.show');
});

require __DIR__.'/settings.php';
require __DIR__.'/s3.php';
