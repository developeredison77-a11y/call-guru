<?php

namespace App\Enums;

enum UserStatusEnum: int
{
    case Inactive = 0;
    case Active = 1;

    public function label(): string
    {
        return match ($this) {
            self::Inactive => 'Inactive',
            self::Active => 'Active',
        };
    }
}
