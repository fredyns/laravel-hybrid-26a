<?php

use App\Enums\UserRole;

test('the user endpoint exposes sanctum roles and permissions', function () {
    $admin = apiAdminUser();

    $this->actingAs($admin, 'sanctum');

    $this->getJson(route('api.user'))
        ->assertOk()
        ->assertJsonPath('roles.0', UserRole::SUPER_ADMIN->value)
        ->assertJsonFragment(['permissions' => collect([
            'rbac.create', 'rbac.delete', 'rbac.index', 'rbac.show', 'rbac.update',
            'users.change-password', 'users.create', 'users.delete', 'users.index', 'users.show', 'users.update',
        ])->sort()->values()->all()]);
});
