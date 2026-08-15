<?php

use App\Enums\UserRole;
use App\Models\RBAC\Role;
use App\Models\User;

test('the role show page renders', function () {
    $this->actingAs(adminUser());

    $role = Role::where('name', UserRole::SUPER_ADMIN->value)->where('guard_name', 'web')->firstOrFail();

    $this->get(route('roles.show', $role))
        ->assertOk()
        ->assertSee('super-admin');
});

test('the role show page is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $role = Role::where('name', UserRole::SUPER_ADMIN->value)->where('guard_name', 'web')->firstOrFail();

    $this->get(route('roles.show', $role))->assertForbidden();
});

test('guests receive 401 for the show role api', function () {
    $role = Role::where('guard_name', 'sanctum')->first();

    $this->getJson(route('api.roles.show', $role))->assertUnauthorized();
});

test('users without permission receive 403 for the show role api', function () {
    $role = Role::where('guard_name', 'sanctum')->first();

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->getJson(route('api.roles.show', $role))->assertForbidden();
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
