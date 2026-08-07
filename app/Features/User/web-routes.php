<?php

use App\Features\User\Create\CreateUserPage;
use App\Features\User\Index\IndexUsersPage;
use App\Features\User\Show\ShowUserWebController;
use App\Features\User\Update\EditUserPage;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::livewire('users', IndexUsersPage::class)
        ->middleware('can:users.index')
        ->name('users.index');

    Route::livewire('users/create', CreateUserPage::class)
        ->middleware('can:users.create')
        ->name('users.create');

    Route::get('users/{user}', ShowUserWebController::class)
        ->middleware('can:users.show')
        ->name('users.show');

    Route::livewire('users/{user}/edit', EditUserPage::class)
        ->middleware('can:users.update')
        ->name('users.edit');
});
