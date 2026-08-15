<?php

use App\Features\Notification\Index\IndexNotificationsPage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('the page deletes a notification', function () {
    $user = apiPlainUser();
    $notificationId = notifyUser($user);

    $this->actingAs($user);

    Livewire::test(IndexNotificationsPage::class)
        ->call('delete', $notificationId);

    expect($user->notifications()->count())->toBe(0);
});

test('the page cannot delete another user\'s notification', function () {
    $other = apiPlainUser();
    $notificationId = notifyUser($other);

    $this->actingAs(apiPlainUser());

    $page = Livewire::test(IndexNotificationsPage::class);

    expect(fn () => $page->call('delete', $notificationId))
        ->toThrow(ModelNotFoundException::class);

    expect($other->notifications()->count())->toBe(1);
});

test('guests receive 401 for the delete notification api', function () {
    $user = apiPlainUser();
    $notificationId = notifyUser($user);

    $this->deleteJson(route('api.notifications.destroy', $notificationId))->assertUnauthorized();
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

test('users cannot delete notifications belonging to others', function () {
    $other = apiPlainUser();
    $notificationId = notifyUser($other);

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->deleteJson(route('api.notifications.destroy', $notificationId))->assertNotFound();

    expect($other->notifications()->count())->toBe(1);
});
