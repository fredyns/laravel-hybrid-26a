<?php

use App\Models\User;

test('guests receive 401 for the delete user api', function () {
    $user = User::factory()->create();

    $this->deleteJson(route('api.users.destroy', $user))->assertUnauthorized();
});

test('users without permission receive 403 for the delete user api', function () {
    $target = User::factory()->create();

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->deleteJson(route('api.users.destroy', $target))->assertForbidden();
});

test('destroy deletes a user', function () {
    $target = User::factory()->create();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->deleteJson(route('api.users.destroy', $target))
        ->assertOk()
        ->assertJsonPath('message', 'User deleted');

    expect(User::find($target->id))->toBeNull();
});

test('users cannot delete their own account', function () {
    $admin = apiAdminUser();

    $this->actingAs($admin, 'sanctum');

    $this->deleteJson(route('api.users.destroy', $admin))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'You cannot delete your own account.');

    expect(User::find($admin->id))->not->toBeNull();
});
