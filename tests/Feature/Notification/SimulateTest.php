<?php

use App\Features\Notification\Index\IndexNotificationsPage;
use App\Models\User;
use Livewire\Livewire;

test('the simulate button creates a notification linking to the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(IndexNotificationsPage::class)
        ->call('simulate');

    expect($user->notifications()->count())->toBe(1);

    $data = $user->notifications()->first()->data;

    expect($data['url'])->toBe(route('dashboard'))
        ->and($data['title'])->toBe('Simulated notification')
        ->and($data['variant'])->toBe('info');
});

test('the simulated notification appears on the page as unread', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(IndexNotificationsPage::class)
        ->assertSee('No notifications yet.')
        ->call('simulate')
        ->assertSee('Simulated notification')
        ->assertSee('Unread');

    expect($user->unreadNotifications()->count())->toBe(1);
});

test('the simulate button only notifies the acting user', function () {
    $other = User::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(IndexNotificationsPage::class)
        ->call('simulate');

    expect($user->notifications()->count())->toBe(1)
        ->and($other->notifications()->count())->toBe(0);
});
