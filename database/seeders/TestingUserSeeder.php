<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TestingUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with testing users.
     */
    public function run(): void
    {
        $users = require database_path('collections/users.testing.php');
        $existingEmails = DB::table('users')
            ->whereIn('email', array_column($users, 'email'))
            ->pluck('email')
            ->toArray();

        foreach ($users as $user) {
            if (in_array($user['email'], $existingEmails)) {
                continue;
            }

            $role = Arr::pull($user, 'role');
            $user = User::factory()->create($user);
            $user->addRoleName($role);
        }
    }
}
