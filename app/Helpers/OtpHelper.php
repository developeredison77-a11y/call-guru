<?php

namespace App\Helpers;

class OtpHelper
{
    public static function generate(int $length = 4): string
    {
        $min = (10 ** ($length - 1));
        $max = (10 ** $length) - 1;

        return (string) random_int($min, $max);
    }
}

