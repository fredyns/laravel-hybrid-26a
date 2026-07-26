<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

return [
    [
        'name' => 'Sys-Admin',
        'email' => 'admin@admin.com',
        'password' => Hash::make('admin'),
        'role' => UserRole::SUPER_ADMIN,
    ],
    [
        'name' => 'Sys-Employee',
        'email' => 'employee@employee.com',
        'password' => Hash::make('password'),
        'role' => UserRole::INTERNAL,
    ],
    [
        'name' => 'Sys-User',
        'email' => 'user@user.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => UserRole::EXTERNAL,
    ],
];
