<?php

namespace App\Features\User\Update;

use App\Models\User;

class UpdateUser
{
    /**
     * @param  array{name: string, email: string}  $input
     */
    public function execute(User $user, array $input): User
    {
        $user->fill([
            'name' => $input['name'],
            'email' => $input['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }
}
