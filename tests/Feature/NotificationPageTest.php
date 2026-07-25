<?php

use App\Models\User;
use App\Notifications\ResourceNotification;

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
