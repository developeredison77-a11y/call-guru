<?php

namespace App\Enums;

enum SexEnum: string
{
    case Male = 'Male';
    case Female = 'Female';
    case Other = 'Other';

    public static function values(): array
    {
        return array_map(static fn (self $value) => $value->value, self::cases());
    }
}

