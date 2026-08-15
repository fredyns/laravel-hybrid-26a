<?php

namespace App\Features\Notification\Delete;

use App\Models\User;

class DeleteNotification
{
    /**
     * Scoped to the owner: an id belonging to another user resolves to 404.
     */
    public function execute(User $user, string $notificationId): void
    {
        $user->notifications()->findOrFail($notificationId)->delete();
    }
}
