<?php

namespace App\Features\Notification\NotifyAdminsOfNewUser;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\ResourceNotification;

class NotifyAdminsOfNewUser
{
    public function execute(User $newUser): void
    {
        $admins = User::role(UserRole::SUPER_ADMIN->value)
            ->whereKeyNot($newUser->getKey())
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new ResourceNotification(
                title: __('User created'),
                body: __(':name was added.', ['name' => $newUser->name]),
                url: route('users.show', $newUser),
            ));
        }
    }
}
