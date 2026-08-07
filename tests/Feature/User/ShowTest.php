<?php

use App\Models\User;

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

test('guests receive 401 for the show user api', function () {
    $user = User::factory()->create();

    $this->getJson(route('api.users.show', $user))->assertUnauthorized();
});

test('users without permission receive 403 for the show user api', function () {
    $target = User::factory()->create();

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->getJson(route('api.users.show', $target))->assertForbidden();
});

test('show returns a single user', function () {
    $target = User::factory()->create();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.users.show', $target))
        ->assertOk()
        ->assertJsonPath('data.id', (string) $target->id)
        ->assertJsonPath('data.email', $target->email);
});
