<?php

use App\Features\Notification\Index\IndexNotificationsPage;
use Livewire\Livewire;

test('the page marks every notification as read', function () {
    $user = apiPlainUser();
    notifyUser($user, 'One');
    notifyUser($user, 'Two');

    $this->actingAs($user);

    Livewire::test(IndexNotificationsPage::class)
        ->call('markAllRead');

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('the page leaves other users notifications untouched', function () {
    $other = apiPlainUser();
    notifyUser($other);

    $user = apiPlainUser();
    notifyUser($user);

    $this->actingAs($user);

    Livewire::test(IndexNotificationsPage::class)
        ->call('markAllRead');

    expect($user->unreadNotifications()->count())->toBe(0)
        ->and($other->unreadNotifications()->count())->toBe(1);
});

test('guests receive 401 for the mark all read api', function () {
    $this->postJson(route('api.notifications.read-all'))->assertUnauthorized();
});

test('mark all read clears every unread notification', function () {
    $user = apiPlainUser();
    notifyUser($user, 'One');
    notifyUser($user, 'Two');

    $this->actingAs($user, 'sanctum');

    $this->postJson(route('api.notifications.read-all'))
        ->assertOk()
        ->assertJsonPath('message', 'All notifications marked as read');

    expect($user->unreadNotifications()->count())->toBe(0);
});
