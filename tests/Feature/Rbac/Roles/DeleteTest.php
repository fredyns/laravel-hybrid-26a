<?php

use App\Enums\UserRole;
use App\Models\RBAC\Role;

test('guests receive 401 for the delete role api', function () {
    $role = Role::where('guard_name', 'sanctum')->first();

    $this->deleteJson(route('api.roles.destroy', $role))->assertUnauthorized();
});

test('users without permission receive 403 for the delete role api', function () {
    $role = Role::where('guard_name', 'sanctum')->first();

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->deleteJson(route('api.roles.destroy', $role))->assertForbidden();
});

test('destroy deletes an unprotected role without users', function () {
    $role = Role::create(['name' => 'deletable', 'guard_name' => 'sanctum']);

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->deleteJson(route('api.roles.destroy', $role))
        ->assertOk()
        ->assertJsonPath('message', 'Role deleted');

    expect(Role::find($role->id))->toBeNull();
});

test('destroy rejects protected roles', function () {
    $role = Role::where('name', UserRole::SUPER_ADMIN->value)->where('guard_name', 'sanctum')->first();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->deleteJson(route('api.roles.destroy', $role))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Protected roles cannot be deleted.');
});

test('destroy rejects roles that still have users', function () {
    $role = Role::create(['name' => 'occupied', 'guard_name' => 'sanctum']);
    $user = apiPlainUser();
    $user->addRole($role);

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->deleteJson(route('api.roles.destroy', $role))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot delete a role that still has assigned users.');
});
