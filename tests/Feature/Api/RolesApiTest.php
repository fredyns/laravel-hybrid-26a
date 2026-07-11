<?php

use App\Enums\UserRole;
use App\Models\RBAC\Role;

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

test('guests receive 401 for role endpoints', function () {
    $role = Role::where('guard_name', 'sanctum')->first();

    $this->getJson(route('api.roles.index'))->assertUnauthorized();
    $this->postJson(route('api.roles.store'))->assertUnauthorized();
    $this->getJson(route('api.roles.show', $role))->assertUnauthorized();
    $this->getJson(route('api.permissions.index'))->assertUnauthorized();
});

test('users without permission receive 403 for role endpoints', function () {
    $role = Role::where('guard_name', 'sanctum')->first();

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->getJson(route('api.roles.index'))->assertForbidden();
    $this->postJson(route('api.roles.store'))->assertForbidden();
    $this->getJson(route('api.roles.show', $role))->assertForbidden();
    $this->putJson(route('api.roles.update', $role))->assertForbidden();
    $this->deleteJson(route('api.roles.destroy', $role))->assertForbidden();
    $this->getJson(route('api.permissions.index'))->assertForbidden();
});

test('roles index returns paginated roles with counts and protected flag', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.roles.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'name', 'guard_name', 'protected', 'permissions_count', 'users_count', 'created_at']],
            'meta' => ['total'],
        ])
        ->assertJsonPath('meta.total', Role::count());
});

test('roles index filters by guard', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $response = $this->getJson(route('api.roles.index', ['guard' => 'web']))->assertOk();

    expect(collect($response->json('data'))->pluck('guard_name')->unique()->all())->toBe(['web']);
});

test('store creates a role with permissions', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->postJson(route('api.roles.store'), [
        'name' => 'auditor',
        'guard' => 'sanctum',
        'permissions' => ['users.index', 'users.show'],
    ])
        ->assertCreated()
        ->assertJsonPath('role.name', 'auditor')
        ->assertJsonPath('role.permissions_count', 2);

    expect(Role::where('name', 'auditor')->where('guard_name', 'sanctum')->exists())->toBeTrue();
});

test('store rejects a duplicate name within the same guard', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->postJson(route('api.roles.store'), [
        'name' => UserRole::SUPER_ADMIN->value,
        'guard' => 'sanctum',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('store rejects unknown permissions', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->postJson(route('api.roles.store'), [
        'name' => 'mismatched',
        'guard' => 'sanctum',
        'permissions' => ['does.not-exist'],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['permissions.0']);
});

test('show returns a role with its permissions', function () {
    $role = Role::where('name', UserRole::SUPER_ADMIN->value)->where('guard_name', 'sanctum')->first();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.roles.show', $role))
        ->assertOk()
        ->assertJsonPath('data.name', UserRole::SUPER_ADMIN->value)
        ->assertJsonPath('data.protected', true)
        ->assertJsonStructure(['data' => ['permissions']]);
});

test('update renames an unprotected role and syncs permissions', function () {
    $role = Role::create(['name' => 'temp-role', 'guard_name' => 'sanctum']);

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->putJson(route('api.roles.update', $role), [
        'name' => 'renamed-role',
        'guard' => 'sanctum',
        'permissions' => ['users.index'],
    ])
        ->assertOk()
        ->assertJsonPath('role.name', 'renamed-role')
        ->assertJsonPath('role.permissions_count', 1);
});

test('update keeps protected role names locked but syncs permissions', function () {
    $role = Role::where('name', UserRole::INTERNAL->value)->where('guard_name', 'sanctum')->first();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->putJson(route('api.roles.update', $role), [
        'name' => 'hacked-name',
        'guard' => 'sanctum',
        'permissions' => ['users.index'],
    ])->assertOk();

    $role->refresh();
    expect($role->name)->toBe(UserRole::INTERNAL->value)
        ->and($role->permissions->pluck('name')->all())->toBe(['users.index']);
});

test('the super-admin role must keep at least one permission', function () {
    $role = Role::where('name', UserRole::SUPER_ADMIN->value)->where('guard_name', 'sanctum')->first();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->putJson(route('api.roles.update', $role), [
        'name' => UserRole::SUPER_ADMIN->value,
        'guard' => 'sanctum',
        'permissions' => [],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['permissions']);
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

test('permissions index returns grouped permissions for a guard', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.permissions.index', ['guard' => 'sanctum']))
        ->assertOk()
        ->assertJsonPath('data.users.0', 'users.index')
        ->assertJsonPath('data.rbac.0', 'rbac.index');
});

test('permissions index requires a valid guard', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.permissions.index'))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['guard']);
});
