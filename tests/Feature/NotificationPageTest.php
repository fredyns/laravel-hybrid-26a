<?php

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

    $component = Livewire::actingAs($user)->test('pages::notifications.index');

    $component->assertSee('Deploy finished')
        ->assertSee('User joined');
});

test('mark individual notification as read', function () {
    $user = User::factory()->create();
    $user->notify(new ResourceNotification('Alert', 'Something happened.'));

    $notification = $user->notifications()->first();
    expect($notification->read_at)->toBeNull();

    $component = Livewire::actingAs($user)->test('pages::notifications.index');
    $component->call('markAsRead', $notification->id);

    expect($notification->fresh()->read_at)->not->toBeNull();
    expect($component->instance()->unreadCount())->toBe(0);
});

test('mark all notifications as read from the page', function () {
    $user = User::factory()->create();
    $user->notify(new ResourceNotification('One', 'First'));
    $user->notify(new ResourceNotification('Two', 'Second'));

    expect($user->unreadNotifications()->count())->toBe(2);

    $component = Livewire::actingAs($user)->test('pages::notifications.index');
    $component->call('markAllRead');

    expect($user->refresh()->unreadNotifications()->count())->toBe(0);
});

test('delete a notification', function () {
    $user = User::factory()->create();
    $user->notify(new ResourceNotification('Temp', 'Will be deleted.'));

    $notification = $user->notifications()->first();

    $component = Livewire::actingAs($user)->test('pages::notifications.index');
    $component->call('deleteNotification', $notification->id);

    expect($user->notifications()->count())->toBe(0);
});

test('notifications page shows empty state', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test('pages::notifications.index');

    $component->assertSee('No notifications yet.');
});

test('simulate notification component creates a notification', function () {
    $user = User::factory()->create();

    expect($user->notifications()->count())->toBe(0);

    Livewire::actingAs($user)->test('simulate-notification')
        ->call('simulate');

    expect($user->notifications()->count())->toBe(1);
});

test('notifications page requires authentication', function () {
    $this->get(route('notifications.index'))
        ->assertRedirect(route('login'));
});
