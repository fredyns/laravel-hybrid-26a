<?php

use App\Features\Notification\Index\IndexNotificationsPage;
use App\Models\User;
use App\Notifications\ResourceNotification;
use Livewire\Livewire;

test('notifications page renders for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Notifications');
});

test('notifications page lists the user\'s notifications', function () {
    $user = User::factory()->create();
    $user->notify(new ResourceNotification('Deploy finished', 'Production deploy completed.'));
    $user->notify(new ResourceNotification('User joined', 'Alice registered.'));

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Deploy finished')
        ->assertSee('User joined');
});

test('notifications page shows empty state', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('No notifications yet.');
});

test('notifications page requires authentication', function () {
    $this->get(route('notifications.index'))
        ->assertRedirect(route('login'));
});

test('the page does not list other users notifications', function () {
    $other = User::factory()->create();
    $other->notify(new ResourceNotification('Secret', 'Not yours.'));

    $this->actingAs(User::factory()->create());

    Livewire::test(IndexNotificationsPage::class)
        ->assertDontSee('Secret')
        ->assertSee('No notifications yet.');
});

test('guests receive 401 for the notifications index api', function () {
    $this->getJson(route('api.notifications.index'))->assertUnauthorized();
});

test('index returns own notifications with unread count', function () {
    $user = apiPlainUser();
    notifyUser($user, 'First');
    notifyUser($user, 'Second');

    $this->actingAs($user, 'sanctum');

    $this->getJson(route('api.notifications.index'))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('unread_count', 2)
        ->assertJsonStructure(['data' => [['id', 'data' => ['title', 'body', 'variant'], 'read_at', 'created_at']]]);
});

test('index does not leak other users notifications', function () {
    $other = apiPlainUser();
    notifyUser($other);

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->getJson(route('api.notifications.index'))
        ->assertOk()
        ->assertJsonCount(0, 'data')
        ->assertJsonPath('unread_count', 0);
});
