<?php

namespace App\Support\PhoneNumbers;

class IndianMobileNumber
{
    public const COUNTRY_CODE = '+91';

    public const PATTERN = '/^[6-9][0-9]{9}$/';

    public static function isValidCountryCode(string $countryCode): bool
    {
        return $countryCode === self::COUNTRY_CODE;
    }

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

    public static function countryCodeValidationRules(): array
    {
        return ['required', 'string', 'in:'.self::COUNTRY_CODE];
    }
}
