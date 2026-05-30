<?php

namespace App\Enums;

enum UserTypeEnum: int
{
    case SuperAdmin = 1;
    case Guruji = 2;
    case Listener = 3;

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Guruji => 'Guruji',
            self::Listener => 'Listener',
        };
    }
}
