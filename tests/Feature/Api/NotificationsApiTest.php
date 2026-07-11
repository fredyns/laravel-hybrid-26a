<?php

use App\Models\User;
use App\Notifications\ResourceNotification;

function notifyUser(User $user, string $title = 'Test notification'): string
{
    $user->notifyNow(new ResourceNotification($title, 'Body text', null, 'info'), ['database']);

    return $user->notifications()->latest()->first()->id;
}

test('guests receive 401 for notification endpoints', function () {
    $this->getJson(route('api.notifications.index'))->assertUnauthorized();
    $this->postJson(route('api.notifications.read-all'))->assertUnauthorized();
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

test('mark read sets the read timestamp', function () {
    $user = apiPlainUser();
    $notificationId = notifyUser($user);

    $this->actingAs($user, 'sanctum');

    $this->putJson(route('api.notifications.read', $notificationId))
        ->assertOk()
        ->assertJsonPath('message', 'Notification marked as read');

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('mark all read clears every unread notification', function () {
    $user = apiPlainUser();
    notifyUser($user, 'One');
    notifyUser($user, 'Two');

    $this->actingAs($user, 'sanctum');

    $this->postJson(route('api.notifications.read-all'))->assertOk();

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('destroy deletes a notification', function () {
    $user = apiPlainUser();
    $notificationId = notifyUser($user);

    $this->actingAs($user, 'sanctum');

    $this->deleteJson(route('api.notifications.destroy', $notificationId))
        ->assertOk()
        ->assertJsonPath('message', 'Notification deleted');

    expect($user->notifications()->count())->toBe(0);
});

test('users cannot touch notifications belonging to others', function () {
    $other = apiPlainUser();
    $notificationId = notifyUser($other);

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->putJson(route('api.notifications.read', $notificationId))->assertNotFound();
    $this->deleteJson(route('api.notifications.destroy', $notificationId))->assertNotFound();

    expect($other->notifications()->count())->toBe(1);
});
