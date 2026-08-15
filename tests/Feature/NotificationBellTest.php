<?php

use App\Models\User;
use App\Notifications\ResourceNotification;

/**
 * The bell is a shared layout component (resources/views/components/notification-bell.blade.php),
 * so it is exercised through a page that renders the app layout.
 */
test('the bell shows no badge when there is nothing unread', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Notifications')
        ->assertDontSee('unread notifications');
});

test('the bell badge counts unread notifications', function () {
    $user = User::factory()->create();
    $user->notify(new ResourceNotification('One', 'First'));
    $user->notify(new ResourceNotification('Two', 'Second'));

    $this->actingAs($user);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('2 unread notifications');
});

test('the bell badge caps the label at nine plus', function () {
    $user = User::factory()->create();

    foreach (range(1, 11) as $i) {
        $user->notify(new ResourceNotification("Item {$i}", 'Body'));
    }

    $this->actingAs($user);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('9+')
        ->assertSee('11 unread notifications');
});

test('the badge ignores notifications already read', function () {
    $user = User::factory()->create();
    $user->notify(new ResourceNotification('One', 'First'));
    $user->notify(new ResourceNotification('Two', 'Second'));
    $user->unreadNotifications->markAsRead();

    $this->actingAs($user);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('unread notifications');
});
