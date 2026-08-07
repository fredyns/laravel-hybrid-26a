<?php

use App\Features\User\Update\EditUserPage;
use App\Models\User;
use Livewire\Livewire;

test('the edit page renders for authorized user', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create();

    $this->get(route('users.edit', $user))->assertOk();
});

test('the edit page is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $user = User::factory()->create();

    $this->get(route('users.edit', $user))->assertForbidden();
});

test('the edit form updates the user', function () {
    $this->actingAs(adminUser());

    $target = User::factory()->create();

    Livewire::test(EditUserPage::class, ['user' => $target])
        ->set('name', 'Renamed User')
        ->set('email', 'renamed@example.com')
        ->call('save')
        ->assertHasNoErrors();

    $target->refresh();

    expect($target->name)->toBe('Renamed User')
        ->and($target->email)->toBe('renamed@example.com')
        ->and($target->email_verified_at)->toBeNull();
});

test('the edit form keeps verification when email is unchanged', function () {
    $this->actingAs(adminUser());

    $target = User::factory()->create();

    Livewire::test(EditUserPage::class, ['user' => $target])
        ->set('name', 'Renamed Only')
        ->call('save')
        ->assertHasNoErrors();

    expect($target->refresh()->email_verified_at)->not->toBeNull();
});

test('guests receive 401 for the update user api', function () {
    $user = User::factory()->create();

    $this->putJson(route('api.users.update', $user))->assertUnauthorized();
});

test('users without permission receive 403 for the update user api', function () {
    $target = User::factory()->create();

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->putJson(route('api.users.update', $target))->assertForbidden();
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
