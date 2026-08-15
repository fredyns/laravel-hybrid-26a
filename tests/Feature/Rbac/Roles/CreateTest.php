<?php

use App\Enums\UserRole;
use App\Features\Rbac\Roles\Create\CreateRolePage;
use App\Models\RBAC\Role;
use App\Models\User;
use Livewire\Livewire;

test('the create role page renders for authorized user', function () {
    $this->actingAs(adminUser());

    $this->get(route('roles.create'))->assertOk();
});

test('the create role page is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('roles.create'))->assertForbidden();
});

test('the create form creates a role with permissions', function () {
    $this->actingAs(adminUser());

    Livewire::test(CreateRolePage::class)
        ->set('guard', 'sanctum')
        ->set('name', 'auditor')
        ->set('permissions', ['users.index', 'users.show'])
        ->call('save')
        ->assertHasNoErrors();

    $role = Role::where('name', 'auditor')->where('guard_name', 'sanctum')->first();

    expect($role)->not->toBeNull()
        ->and($role->permissions->pluck('name')->sort()->values()->all())->toBe(['users.index', 'users.show']);
});

test('the create form rejects a duplicate name within the same guard', function () {
    $this->actingAs(adminUser());

    Livewire::test(CreateRolePage::class)
        ->set('guard', 'sanctum')
        ->set('name', UserRole::SUPER_ADMIN->value)
        ->call('save')
        ->assertHasErrors(['name']);
});

test('changing the guard clears the selected permissions', function () {
    $this->actingAs(adminUser());

    Livewire::test(CreateRolePage::class)
        ->set('guard', 'sanctum')
        ->set('permissions', ['users.index'])
        ->set('guard', 'web')
        ->assertSet('permissions', []);
});

test('guests receive 401 for the create role api', function () {
    $this->postJson(route('api.roles.store'))->assertUnauthorized();
});

test('users without permission receive 403 for the create role api', function () {
    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->postJson(route('api.roles.store'))->assertForbidden();
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
