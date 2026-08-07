<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('guests receive 401 for the change password api', function () {
    $user = User::factory()->create();

    $this->putJson(route('api.users.change-password', $user))->assertUnauthorized();
});

test('users without permission receive 403 for the change password api', function () {
    $target = User::factory()->create();

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->putJson(route('api.users.change-password', $target))->assertForbidden();
});

test('change password updates the user password', function () {
    $target = User::factory()->create();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->putJson(route('api.users.change-password', $target), [
        'password' => 'changed-password-1',
        'password_confirmation' => 'changed-password-1',
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Password updated');

    expect(Hash::check('changed-password-1', $target->refresh()->password))->toBeTrue();
});

test('change password requires confirmation', function () {
    $target = User::factory()->create();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->putJson(route('api.users.change-password', $target), [
        'password' => 'changed-password-1',
        'password_confirmation' => 'does-not-match',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});
