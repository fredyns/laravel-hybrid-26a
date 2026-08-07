<?php

namespace App\Features\User\PurgeUnverified;

use App\Models\User;

class PurgeUnverifiedUsers
{
    /**
     * Delete accounts that never verified their email address.
     *
     * Iterates and deletes per model so lifecycle events fire
     * (spatie/laravel-permission detaches roles on delete).
     */
    public function execute(int $days = 30): int
    {
        $count = 0;

        User::query()
            ->whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDays($days))
            ->each(function (User $user) use (&$count): void {
                $user->delete();
                $count++;
            });

        return $count;
    }
}
