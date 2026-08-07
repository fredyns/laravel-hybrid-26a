<?php

use App\Models\User;
use App\Notifications\ResourceNotification;
use Illuminate\Support\Facades\Notification;

test('creating a user notifies the other super admins', function () {
    Notification::fake();

    $actingAdmin = apiAdminUser();
    $otherAdmin = apiAdminUser();

    $this->actingAs($actingAdmin, 'sanctum');

    $this->postJson(route('api.users.store'), [
        'name' => 'Fresh User',
        'email' => 'fresh.user@example.com',
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])->assertCreated();

    Notification::assertSentTo(
        [$actingAdmin, $otherAdmin],
        ResourceNotification::class,
        fn (ResourceNotification $notification) => $notification->title === __('User created')
    );
});

test('the new user is not notified about their own creation', function () {
    Notification::fake();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->postJson(route('api.users.store'), [
        'name' => 'Fresh User',
        'email' => 'fresh.user@example.com',
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])->assertCreated();

    $newUser = User::where('email', 'fresh.user@example.com')->firstOrFail();

    Notification::assertNotSentTo($newUser, ResourceNotification::class);
});
