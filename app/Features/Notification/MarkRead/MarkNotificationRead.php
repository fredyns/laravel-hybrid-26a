<?php

namespace App\Features\Notification\MarkRead;

use App\Models\User;

class MarkNotificationRead
{
    /**
     * Scoped to the owner: an id belonging to another user resolves to 404.
     */
    public function execute(User $user, string $notificationId): void
    {
        $user->notifications()->findOrFail($notificationId)->markAsRead();
    }
}
