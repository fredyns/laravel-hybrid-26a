<?php

namespace App\Features\Notification\NotifyAdminsOfNewUser;

use App\Features\User\Events\UserCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreatedListener implements ShouldQueue
{
    public function __construct(private NotifyAdminsOfNewUser $action) {}

    /**
     * The queued payload serializes the User model; if the user was deleted
     * before the queue processed this job, the job fails with a
     * ModelNotFoundException, which is acceptable here.
     */
    public function handle(UserCreated $event): void
    {
        $this->action->execute($event->user);
    }
}
