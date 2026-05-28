<?php

namespace App\Support\PhoneNumbers;

class IndianMobileNumber
{
    public const PATTERN = '/^[6-9][0-9]{9}$/';

    public static function isValid(string $mobileNumber): bool
    {
        return preg_match(self::PATTERN, $mobileNumber) === 1;
    }

    public static function validationRules(bool $unique = false): array
    {
        $rules = ['required', 'numeric', 'digits:10', 'regex:'.self::PATTERN];

        if ($unique) {
            $rules[] = 'unique:users,mobile_number';
        }

        return $rules;
    }
}
