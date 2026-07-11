<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('guests receive 401 for settings endpoints', function () {
    $this->putJson(route('api.settings.profile'))->assertUnauthorized();
    $this->putJson(route('api.settings.password'))->assertUnauthorized();
    $this->deleteJson(route('api.settings.account'))->assertUnauthorized();
});

test('profile update changes name and email and resets verification', function () {
    $user = apiPlainUser();
    expect($user->email_verified_at)->not->toBeNull();

    $this->actingAs($user, 'sanctum');

    $this->putJson(route('api.settings.profile'), [
        'name' => 'Renamed Self',
        'email' => 'renamed.self@example.com',
    ])
        ->assertOk()
        ->assertJsonPath('user.name', 'Renamed Self');

    $user->refresh();
    expect($user->email)->toBe('renamed.self@example.com')
        ->and($user->email_verified_at)->toBeNull();
});

test('profile update rejects an email taken by another user', function () {
    $other = User::factory()->create();
    $user = apiPlainUser();

    $this->actingAs($user, 'sanctum');

    $this->putJson(route('api.settings.profile'), [
        'name' => $user->name,
        'email' => $other->email,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('password update requires the correct current password', function () {
    $user = apiPlainUser();

    $this->actingAs($user, 'sanctum');

    $this->putJson(route('api.settings.password'), [
        'current_password' => 'not-the-password',
        'password' => 'my-new-password-1',
        'password_confirmation' => 'my-new-password-1',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['current_password']);
});

test('password update changes the password', function () {
    $user = apiPlainUser();

    $this->actingAs($user, 'sanctum');

    $this->putJson(route('api.settings.password'), [
        'current_password' => 'password',
        'password' => 'my-new-password-1',
        'password_confirmation' => 'my-new-password-1',
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Password updated');

    expect(Hash::check('my-new-password-1', $user->refresh()->password))->toBeTrue();
});

test('account deletion requires the correct password', function () {
    $user = apiPlainUser();

    $this->actingAs($user, 'sanctum');

    $this->deleteJson(route('api.settings.account'), [
        'password' => 'wrong-password',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);

    expect(User::find($user->id))->not->toBeNull();
});

test('account deletion removes the user and their tokens', function () {
    $user = apiPlainUser();
    $user->createToken('api-token');

    $this->actingAs($user, 'sanctum');

    $this->deleteJson(route('api.settings.account'), [
        'password' => 'password',
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Account deleted');

    expect(User::find($user->id))->toBeNull()
        ->and($user->tokens()->count())->toBe(0);
});
