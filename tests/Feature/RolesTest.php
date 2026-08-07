<?php

use App\Models\RBAC\Role;
use App\Models\User;

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

test('index can filter roles by guard', function () {
    $this->actingAs(adminUser());

    Role::create(['name' => 'sanctum-only-role', 'guard_name' => 'sanctum']);

    $this->get(route('roles.index', ['guard' => 'sanctum']))
        ->assertOk();

    $this->get(route('roles.index', ['guard' => 'web']))
        ->assertOk();

    $this->get(route('roles.index'))
        ->assertOk();
});

test('the role show page renders', function () {
    $this->actingAs(adminUser());

    $role = Role::where('name', 'super-admin')->where('guard_name', 'web')->firstOrFail();

    $this->get(route('roles.show', $role))
        ->assertOk()
        ->assertSee('super-admin');
});

test('the role show page is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $role = Role::where('name', 'super-admin')->where('guard_name', 'web')->firstOrFail();

    $this->get(route('roles.show', $role))->assertForbidden();
});
