<?php

namespace App\Features\Notification\Simulate;

use App\Models\User;
use App\Notifications\ResourceNotification;

/**
 * Demo affordance: create a notification on demand so the notification UI can
 * be exercised without waiting for a real domain event.
 *
 * Sent on the database channel only — no broadcast, so it needs no running
 * Reverb server.
 */
class SimulateNotification
{
    public function execute(User $user): void
    {
        $user->notifyNow(new ResourceNotification(
            title: __('Simulated notification'),
            body: __('A demo notification created from the notifications page.'),
            url: route('dashboard'),
            variant: 'info',
        ), ['database']);
    }
}
