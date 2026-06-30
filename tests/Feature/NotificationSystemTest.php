<?php

use App\Models\User;
use App\Notifications\ResourceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Activate the reverb broadcaster for channel-authorization assertions.
 *
 * The TestCase forces BROADCAST_CONNECTION=null for isolation, so channels.php
 * registers its callbacks on the null broadcaster at boot. Switching the
 * default to reverb yields a fresh connection with no channels, so they must be
 * re-registered against it before exercising channel authorization.
 */
function useReverbBroadcaster(): void
{
    config(['broadcasting.default' => 'reverb']);
    Broadcast::purge('null');
    Broadcast::purge('reverb');

    require base_path('routes/channels.php');
}

test('notifying a user stores a database notification with the expected payload', function () {
    $user = User::factory()->create();

    $user->notify(new ResourceNotification(
        title: 'User created',
        body: 'Jane was added.',
        url: '/users/1',
        variant: 'success',
    ));

    expect($user->notifications()->count())->toBe(1);

    $data = $user->notifications()->first()->data;

    expect($data)->toMatchArray([
        'title' => 'User created',
        'body' => 'Jane was added.',
        'url' => '/users/1',
        'variant' => 'success',
    ]);
});

test('the notification is sent on both the database and broadcast channels', function () {
    Notification::fake();

    $user = User::factory()->create();

    $user->notify(new ResourceNotification('Hi', 'There'));

    Notification::assertSentTo(
        $user,
        ResourceNotification::class,
        function (ResourceNotification $notification, array $channels) use ($user) {
            return in_array('database', $channels)
                && in_array('broadcast', $channels)
                && $notification->toArray($user)['title'] === 'Hi';
        },
    );
});

test('broadcast authorization allows the channel owner', function () {
    useReverbBroadcaster();

    $user = User::factory()->create();
    $this->actingAs($user);

    $request = Request::create('/broadcasting/auth', 'POST', [
        'channel_name' => 'private-App.Models.User.'.$user->id,
        'socket_id' => '1234.5678',
    ]);
    $request->setUserResolver(fn () => $user);

    expect(Broadcast::auth($request))->not->toBeEmpty();
});

test('broadcast authorization rejects another user\'s channel', function () {
    useReverbBroadcaster();

    $user = User::factory()->create();
    $other = User::factory()->create();
    $this->actingAs($user);

    $request = Request::create('/broadcasting/auth', 'POST', [
        'channel_name' => 'private-App.Models.User.'.$other->id,
        'socket_id' => '1234.5678',
    ]);
    $request->setUserResolver(fn () => $user);

    expect(fn () => Broadcast::auth($request))->toThrow(AccessDeniedHttpException::class);
});

test('notification bell renders the unread count', function () {
    $user = User::factory()->create();
    $user->notify(new ResourceNotification('One', 'First'));
    $user->notify(new ResourceNotification('Two', 'Second'));

    $this->actingAs($user);

    $component = Livewire::test('notification-bell');

    expect($component->instance()->unreadCount())->toBe(2);

    $component->assertSee('One')->assertSee('Two');
});

test('marking all read zeroes the unread count', function () {
    $user = User::factory()->create();
    $user->notify(new ResourceNotification('One', 'First'));

    $this->actingAs($user);

    $component = Livewire::test('notification-bell');
    expect($component->instance()->unreadCount())->toBe(1);

    $component->call('markAllRead');

    expect($component->instance()->unreadCount())->toBe(0);
    expect($user->refresh()->unreadNotifications()->count())->toBe(0);
});

test('the bell refreshes when a notification-received event is dispatched', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $component = Livewire::test('notification-bell');
    expect($component->instance()->unreadCount())->toBe(0);

    $user->notify(new ResourceNotification('Fresh', 'Just arrived'));

    $component->dispatch('notification-received')
        ->assertSee('Fresh');

    expect($component->instance()->unreadCount())->toBe(1);
});
