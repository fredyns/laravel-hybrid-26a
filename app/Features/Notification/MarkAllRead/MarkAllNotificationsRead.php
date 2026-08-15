<?php

namespace App\Features\Notification\MarkAllRead;

use App\Models\User;

class MarkAllNotificationsRead
{
    public function execute(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }
}
