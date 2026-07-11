<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

function apiAdminUser(): User
{
    $user = User::factory()->create();
    $user->addRoleNames([UserRole::SUPER_ADMIN]);

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user;
}

function apiPlainUser(): User
{
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return User::factory()->create();
}

test('guests receive 401 for every users endpoint', function () {
    $user = User::factory()->create();

    $this->getJson(route('api.users.index'))->assertUnauthorized();
    $this->postJson(route('api.users.store'))->assertUnauthorized();
    $this->getJson(route('api.users.show', $user))->assertUnauthorized();
    $this->putJson(route('api.users.update', $user))->assertUnauthorized();
    $this->deleteJson(route('api.users.destroy', $user))->assertUnauthorized();
    $this->putJson(route('api.users.change-password', $user))->assertUnauthorized();
});

test('users without permission receive 403', function () {
    $target = User::factory()->create();

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->postJson(route('api.users.store'))->assertForbidden();
    $this->getJson(route('api.users.show', $target))->assertForbidden();
    $this->putJson(route('api.users.update', $target))->assertForbidden();
    $this->deleteJson(route('api.users.destroy', $target))->assertForbidden();
    $this->putJson(route('api.users.change-password', $target))->assertForbidden();
});

test('index returns paginated users', function () {
    User::factory()->count(3)->create();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.users.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at']],
            'meta' => ['current_page', 'per_page', 'total', 'from', 'to'],
        ])
        ->assertJsonPath('meta.total', User::count());
});

test('index filters users by search term', function () {
    User::factory()->create(['name' => 'Findable Person']);
    User::factory()->create(['name' => 'Somebody Else']);

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.users.index', ['search' => 'Findable']))
        ->assertOk()
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.name', 'Findable Person');
});

test('index sorts users by the requested column', function () {
    User::factory()->create(['name' => 'Aaa First']);
    User::factory()->create(['name' => 'Zzz Last']);

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.users.index', ['sort_by' => 'name', 'sort_dir' => 'asc']))
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Aaa First');
});

test('index rejects unknown sort columns', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.users.index', ['sort_by' => 'password']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['sort_by']);
});

test('store creates a user with a hashed password', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->postJson(route('api.users.store'), [
        'name' => 'New User',
        'email' => 'new.user@example.com',
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])
        ->assertCreated()
        ->assertJsonPath('message', 'User created')
        ->assertJsonPath('user.email', 'new.user@example.com');

    $user = User::where('email', 'new.user@example.com')->first();
    expect($user)->not->toBeNull()
        ->and(Hash::check('brand-new-password', $user->password))->toBeTrue();
});

test('store validates input', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->postJson(route('api.users.store'), [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'secret',
        'password_confirmation' => 'different',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('store rejects duplicate emails', function () {
    $existing = User::factory()->create();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->postJson(route('api.users.store'), [
        'name' => 'Duplicate',
        'email' => $existing->email,
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('show returns a single user', function () {
    $target = User::factory()->create();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.users.show', $target))
        ->assertOk()
        ->assertJsonPath('data.id', (string) $target->id)
        ->assertJsonPath('data.email', $target->email);
});

test('update changes name and email and resets verification on email change', function () {
    $target = User::factory()->create();
    expect($target->email_verified_at)->not->toBeNull();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->putJson(route('api.users.update', $target), [
        'name' => 'Renamed User',
        'email' => 'renamed@example.com',
    ])
        ->assertOk()
        ->assertJsonPath('user.name', 'Renamed User');

    $target->refresh();
    expect($target->email)->toBe('renamed@example.com')
        ->and($target->email_verified_at)->toBeNull();
});

test('update keeps verification when email is unchanged', function () {
    $target = User::factory()->create();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->putJson(route('api.users.update', $target), [
        'name' => 'Renamed Only',
        'email' => $target->email,
    ])->assertOk();

    expect($target->refresh()->email_verified_at)->not->toBeNull();
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
