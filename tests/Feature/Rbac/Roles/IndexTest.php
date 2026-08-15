<?php

use App\Features\Rbac\Roles\Index\IndexRolesPage;
use App\Models\RBAC\Role;
use App\Models\User;
use Livewire\Livewire;

test('roles index is displayed for authorized user', function () {
    $this->actingAs(adminUser());

    $this->get(route('roles.index'))->assertOk();
});

test('roles index is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('roles.index'))->assertForbidden();
});

test('index can search roles by name', function () {
    $this->actingAs(adminUser());

    Role::create(['name' => 'zentavia-moderator', 'guard_name' => 'web']);

    $this->get(route('roles.index', ['q' => 'zentavia']))
        ->assertOk()
        ->assertSee('zentavia-moderator')
        ->assertDontSee('super-admin');
});

test('index search updates live', function () {
    $this->actingAs(adminUser());

    Role::create(['name' => 'zentavia-moderator', 'guard_name' => 'web']);

    Livewire::test(IndexRolesPage::class)
        ->assertSee('zentavia-moderator')
        ->assertSee('super-admin')
        ->set('search', 'zentavia')
        ->assertSee('zentavia-moderator')
        ->assertDontSee('super-admin');
});

test('index can filter roles by guard', function () {
    $this->actingAs(adminUser());

    Role::create(['name' => 'sanctum-only-role', 'guard_name' => 'sanctum']);

    $this->get(route('roles.index', ['guard' => 'sanctum']))->assertOk();
    $this->get(route('roles.index', ['guard' => 'web']))->assertOk();
    $this->get(route('roles.index'))->assertOk();
});

test('the guard filter only accepts known guards', function () {
    $this->actingAs(adminUser());

    Livewire::test(IndexRolesPage::class)
        ->call('filterByGuard', 'sanctum')
        ->assertSet('guard', 'sanctum')
        ->call('filterByGuard', 'not-a-guard')
        ->assertSet('guard', '');
});

test('guests receive 401 for the roles index api', function () {
    $this->getJson(route('api.roles.index'))->assertUnauthorized();
});

test('users without permission receive 403 for the roles index api', function () {
    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->getJson(route('api.roles.index'))->assertForbidden();
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
