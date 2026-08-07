<?php

use App\Features\User\Create\CreateUserPage;
use App\Features\User\Events\UserCreated;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('the create page renders for authorized user', function () {
    $this->actingAs(adminUser());

    $this->get(route('users.create'))->assertOk();
});

test('the create page is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('users.create'))->assertForbidden();
});

test('the create form creates a user and redirects to its page', function () {
    $this->actingAs(adminUser());

    Livewire::test(CreateUserPage::class)
        ->set('name', 'Form User')
        ->set('email', 'form.user@example.com')
        ->set('password', 'brand-new-password')
        ->set('password_confirmation', 'brand-new-password')
        ->call('save')
        ->assertHasNoErrors();

    $user = User::where('email', 'form.user@example.com')->first();

    expect($user)->not->toBeNull()
        ->and(Hash::check('brand-new-password', $user->password))->toBeTrue();
});

test('the create form validates input', function () {
    $this->actingAs(adminUser());

    Livewire::test(CreateUserPage::class)
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->set('password', 'secret')
        ->set('password_confirmation', 'different')
        ->call('save')
        ->assertHasErrors(['name', 'email', 'password']);

    expect(User::where('email', 'not-an-email')->exists())->toBeFalse();
});

test('creating a user dispatches the UserCreated event', function () {
    Event::fake([UserCreated::class]);

    $this->actingAs(adminUser());

    Livewire::test(CreateUserPage::class)
        ->set('name', 'Event User')
        ->set('email', 'event.user@example.com')
        ->set('password', 'brand-new-password')
        ->set('password_confirmation', 'brand-new-password')
        ->call('save');

    Event::assertDispatched(UserCreated::class, function (UserCreated $event) {
        return $event->user->email === 'event.user@example.com';
    });
});

test('guests receive 401 for the create user api', function () {
    $this->postJson(route('api.users.store'))->assertUnauthorized();
});

test('users without permission receive 403 for the create user api', function () {
    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->postJson(route('api.users.store'))->assertForbidden();
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
