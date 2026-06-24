<?php

namespace App\Enums;

/**
 * Enum for User roles.
 */
enum AuthGuard: string
{
    use EnumTrait;

    case WEB = 'web';
    case SANCTUM = 'sanctum';
}
