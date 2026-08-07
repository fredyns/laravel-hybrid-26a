<?php

namespace App\Features\User\Create;

use App\Features\User\Events\UserCreated;
use App\Models\User;

class CreateUser
{
    /**
     * @param  array{name: string, email: string, password: string}  $input
     */
    public function execute(array $input): User
    {
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);

        UserCreated::dispatch($user);

        return $user;
    }
}
