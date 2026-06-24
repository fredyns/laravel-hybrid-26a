<?php

use Illuminate\Support\Facades\Hash;

return [
    [
        'name' => 'Sys-Admin',
        'email' => 'admin@admin.com',
        'password' => Hash::make('admin'),
    ],
    [
        'name' => 'Sys-Employee',
        'email' => 'employee@employee.com',
        'password' => Hash::make('password'),
    ],
    [
        'name' => 'Sys-User',
        'email' => 'user@user.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
    ],
];
