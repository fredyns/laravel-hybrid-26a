<?php

use App\Enums\UserRole;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

function adminUser(): User
{
    $user = User::factory()->create();
    $user->addRoleNames([UserRole::SUPER_ADMIN]);

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user;
}

function externalUser(): User
{
    $user = User::factory()->create();
    $user->addRoleNames([UserRole::EXTERNAL]);

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user;
}

test('index page is displayed for authorized user', function () {
    $this->actingAs(adminUser());

    $this->get(route('users.index'))->assertOk();
});

test('index page is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('users.index'))->assertForbidden();
});

test('index can search users by name', function () {
    $this->actingAs(adminUser());

    $needle = User::factory()->create(['name' => 'Zentavia Marwick', 'email' => 'zentavia@example.test']);
    $other = User::factory()->create(['name' => 'Bartholomew Quince', 'email' => 'bartholomew@example.test']);

    $this->get(route('users.index', ['q' => 'Zentavia']))
        ->assertOk()
        ->assertSee('Zentavia Marwick')
        ->assertDontSee('Bartholomew Quince');
});

test('index can search users by email', function () {
    $this->actingAs(adminUser());

    $needle = User::factory()->create(['name' => 'Zentavia Marwick', 'email' => 'zentavia@example.test']);
    $other = User::factory()->create(['name' => 'Bartholomew Quince', 'email' => 'bartholomew@example.test']);

    $this->get(route('users.index', ['q' => 'bartholomew@example.test']))
        ->assertOk()
        ->assertSee('Bartholomew Quince')
        ->assertDontSee('Zentavia Marwick');
});

test('index can filter visible columns', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create(['name' => 'Columnar Tester', 'email' => 'columnar@example.test']);

    $this->get(route('users.index', ['columns' => 'name']))
        ->assertOk()
        ->assertSee('Columnar Tester')
        ->assertDontSee('columnar@example.test');

    $this->get(route('users.index', ['columns' => 'name,email']))
        ->assertOk()
        ->assertSee('Columnar Tester')
        ->assertSee('columnar@example.test');
});

test('the individual show page renders', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create(['name' => 'Page Subject']);

    $this->get(route('users.show', $user))
        ->assertOk()
        ->assertSee('Page Subject');
});

test('the individual show page is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $user = User::factory()->create();

    $this->get(route('users.show', $user))->assertForbidden();
});
