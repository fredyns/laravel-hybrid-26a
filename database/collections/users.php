<?php

use Illuminate\Support\Str;

return [
    // super-admin
    [
        'id' => Str::uuid(),
        'name' => 'fredyns',
        'email' => 'dm@fredyns.id',
        'email_verified_at' => now(),
        'password' => '$2y$12$WvjfVopnzLYtfD0AULAFAuVOdF8OOVFb76OjS04Uwc6YgpklOmiy.',
        'remember_token' => Str::random(10),
        'created_at' => now(),
        'updated_at' => now(),
    ],
    // user
    [
        'id' => Str::uuid(),
        'name' => 'fredy',
        'email' => 'fredy.ns@gmail.com',
        'email_verified_at' => now(),
        'password' => '$2y$12$E4fQVLQTXZB6cxVFA20LNemC1HfpAYaNSzEVMvavayvEupOD2fjqe',
        'remember_token' => Str::random(10),
        'created_at' => now(),
        'updated_at' => now(),
    ],
    // employee
    [
        'id' => Str::uuid(),
        'name' => 'Fredy BKI',
        'email' => 'fredy.ns@bki.co.id',
        'email_verified_at' => now(),
        'password' => '$2y$12$E4fQVLQTXZB6cxVFA20LNemC1HfpAYaNSzEVMvavayvEupOD2fjqe',
        'remember_token' => Str::random(10),
        'created_at' => now(),
        'updated_at' => now(),
    ],
    // super-admin
    [
        'id' => Str::uuid(),
        'name' => 'IT BKI',
        'email' => 'ict@bki.co.id',
        'email_verified_at' => now(),
        'password' => '$2a$12$yP/1QjInWzD2FyP.r3cs8ugTSta7Hk6Eq3O9Ca7G1MtWq5HsE95C6',
        'remember_token' => Str::random(10),
        'created_at' => now(),
        'updated_at' => now(),
    ],
];
