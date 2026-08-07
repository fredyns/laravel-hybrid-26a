<?php

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;

test('purge deletes unverified users older than the cutoff', function () {
    $stale = User::factory()->unverified()->create(['created_at' => now()->subDays(40)]);
    $recent = User::factory()->unverified()->create(['created_at' => now()->subDays(5)]);
    $verified = User::factory()->create(['created_at' => now()->subDays(40)]);

    $this->artisan('users:purge-unverified')
        ->expectsOutputToContain('Purged 1 unverified user(s)')
        ->assertSuccessful();

    expect(User::find($stale->id))->toBeNull()
        ->and(User::find($recent->id))->not->toBeNull()
        ->and(User::find($verified->id))->not->toBeNull();
});

test('purge honors the days option', function () {
    $user = User::factory()->unverified()->create(['created_at' => now()->subDays(10)]);

    $this->artisan('users:purge-unverified', ['--days' => 30])->assertSuccessful();
    expect(User::find($user->id))->not->toBeNull();

    $this->artisan('users:purge-unverified', ['--days' => 5])->assertSuccessful();
    expect(User::find($user->id))->toBeNull();
});

test('the purge command is scheduled daily', function () {
    $events = collect(app(Schedule::class)->events());

    expect($events->contains(
        fn ($event) => str_contains((string) $event->command, 'users:purge-unverified')
    ))->toBeTrue();
});
