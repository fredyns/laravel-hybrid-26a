<?php

use App\Enums\UserRole;
use App\Features\Rbac\Roles\Update\EditRolePage;
use App\Models\RBAC\Role;
use App\Models\User;
use Livewire\Livewire;

test('the edit role page renders for authorized user', function () {
    $this->actingAs(adminUser());

    $role = Role::create(['name' => 'temp-role', 'guard_name' => 'web']);

    $this->get(route('roles.edit', $role))->assertOk();
});

test('the edit role page is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $role = Role::create(['name' => 'temp-role', 'guard_name' => 'web']);

    $this->get(route('roles.edit', $role))->assertForbidden();
});

test('the edit form renames an unprotected role and syncs permissions', function () {
    $this->actingAs(adminUser());

    $role = Role::create(['name' => 'temp-role', 'guard_name' => 'sanctum']);

    Livewire::test(EditRolePage::class, ['role' => $role])
        ->set('name', 'renamed-role')
        ->set('permissions', ['users.index'])
        ->call('save')
        ->assertHasNoErrors();

    $role->refresh();

    expect($role->name)->toBe('renamed-role')
        ->and($role->permissions->pluck('name')->all())->toBe(['users.index']);
});

test('the edit form keeps protected role names locked but syncs permissions', function () {
    $this->actingAs(adminUser());

    $role = Role::where('name', UserRole::INTERNAL->value)->where('guard_name', 'sanctum')->firstOrFail();

    Livewire::test(EditRolePage::class, ['role' => $role])
        ->set('name', 'hacked-name')
        ->set('permissions', ['users.index'])
        ->call('save')
        ->assertHasNoErrors();

    $role->refresh();

    expect($role->name)->toBe(UserRole::INTERNAL->value)
        ->and($role->permissions->pluck('name')->all())->toBe(['users.index']);
});

test('the edit form refuses to strip every super-admin permission', function () {
    $this->actingAs(adminUser());

    $role = Role::where('name', UserRole::SUPER_ADMIN->value)->where('guard_name', 'sanctum')->firstOrFail();

    Livewire::test(EditRolePage::class, ['role' => $role])
        ->set('permissions', [])
        ->call('save')
        ->assertHasErrors(['permissions']);

    expect($role->refresh()->permissions)->not->toBeEmpty();
});

test('guests receive 401 for the update role api', function () {
    $role = Role::where('guard_name', 'sanctum')->first();

    $this->putJson(route('api.roles.update', $role))->assertUnauthorized();
});

test('users without permission receive 403 for the update role api', function () {
    $role = Role::where('guard_name', 'sanctum')->first();

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->putJson(route('api.roles.update', $role))->assertForbidden();
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
