<?php

namespace App\Enums;

/**
 * Basic User roles.
 */
enum UserRole: string
{
    use EnumTrait;

    case SUPER_ADMIN = 'super-admin';
    case INTERNAL = 'internal';
    case EXTERNAL = 'external';
}
