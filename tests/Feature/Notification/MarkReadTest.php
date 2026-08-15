<?php

use App\Features\Notification\Index\IndexNotificationsPage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('the page marks a single notification as read', function () {
    $user = apiPlainUser();
    $notificationId = notifyUser($user);

    $this->actingAs($user);

    Livewire::test(IndexNotificationsPage::class)
        ->call('markAsRead', $notificationId);

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('the page cannot mark another user\'s notification as read', function () {
    $other = apiPlainUser();
    $notificationId = notifyUser($other);

    $this->actingAs(apiPlainUser());

    $page = Livewire::test(IndexNotificationsPage::class);

    expect(fn () => $page->call('markAsRead', $notificationId))
        ->toThrow(ModelNotFoundException::class);

    expect($other->unreadNotifications()->count())->toBe(1);
});

test('guests receive 401 for the mark read api', function () {
    $user = apiPlainUser();
    $notificationId = notifyUser($user);

    $this->putJson(route('api.notifications.read', $notificationId))->assertUnauthorized();
});

test('mark read sets the read timestamp', function () {
    $user = apiPlainUser();
    $notificationId = notifyUser($user);

    $this->actingAs($user, 'sanctum');

    $this->putJson(route('api.notifications.read', $notificationId))
        ->assertOk()
        ->assertJsonPath('message', 'Notification marked as read');

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('users cannot mark notifications belonging to others as read', function () {
    $other = apiPlainUser();
    $notificationId = notifyUser($other);

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->putJson(route('api.notifications.read', $notificationId))->assertNotFound();

    expect($other->unreadNotifications()->count())->toBe(1);
});
