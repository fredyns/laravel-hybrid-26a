<?php

use App\Features\Rbac\Permissions\Index\IndexPermissionsApiController;
use App\Features\Rbac\Roles\Create\CreateRoleApiController;
use App\Features\Rbac\Roles\Delete\DeleteRoleApiController;
use App\Features\Rbac\Roles\Index\IndexRolesApiController;
use App\Features\Rbac\Roles\Show\ShowRoleApiController;
use App\Features\Rbac\Roles\Update\UpdateRoleApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('rbac')->group(function (): void {
    Route::prefix('roles')->group(function (): void {
        Route::get('/', IndexRolesApiController::class)
            ->middleware('can:rbac.index')
            ->name('api.roles.index');

        Route::post('/', CreateRoleApiController::class)
            ->middleware('can:rbac.create')
            ->name('api.roles.store');

        Route::get('{role}', ShowRoleApiController::class)
            ->middleware('can:rbac.show')
            ->name('api.roles.show');

        Route::put('{role}', UpdateRoleApiController::class)
            ->middleware('can:rbac.update')
            ->name('api.roles.update');

        Route::delete('{role}', DeleteRoleApiController::class)
            ->middleware('can:rbac.delete')
            ->name('api.roles.destroy');
    });

    Route::get('permissions', IndexPermissionsApiController::class)
        ->middleware('can:rbac.index')
        ->name('api.permissions.index');
});
