<?php

use App\Features\Rbac\MyAccess\MyAccessWebController;
use App\Features\Rbac\Roles\Create\CreateRolePage;
use App\Features\Rbac\Roles\Index\IndexRolesPage;
use App\Features\Rbac\Roles\Show\ShowRoleWebController;
use App\Features\Rbac\Roles\Update\EditRolePage;
use Illuminate\Support\Facades\Route;

// Self-service page: kept at /my-access (outside the /rbac prefix) so its
// public URL is unchanged by the move into this feature.
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('my-access', MyAccessWebController::class)->name('my-access');
});

Route::middleware(['auth', 'verified'])->prefix('rbac')->group(function (): void {
    Route::livewire('roles', IndexRolesPage::class)
        ->middleware('can:rbac.index')
        ->name('roles.index');

    Route::livewire('roles/create', CreateRolePage::class)
        ->middleware('can:rbac.create')
        ->name('roles.create');

    Route::get('roles/{role}', ShowRoleWebController::class)
        ->middleware('can:rbac.show')
        ->name('roles.show');

    Route::livewire('roles/{role}/edit', EditRolePage::class)
        ->middleware('can:rbac.update')
        ->name('roles.edit');
});
