<?php

use App\Enums\UserRole;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

test('guests are redirected to login', function () {
    $this->get(route('my-access'))->assertRedirect(route('login'));
});

test('my access page is displayed for authenticated user', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('my-access'))
        ->assertOk()
        ->assertSee('My Access')
        ->assertSee('You have no roles assigned.')
        ->assertSee('You have no permissions assigned.');
});

test('my access page shows the user roles and permissions', function () {
    $user = User::factory()->create();
    $user->addRoleNames([UserRole::SUPER_ADMIN]);

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $this->actingAs($user);

    $this->get(route('my-access'))
        ->assertOk()
        ->assertSee('Super Admin')
        ->assertDontSee('You have no permissions assigned.');
});
