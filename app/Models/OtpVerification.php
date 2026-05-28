<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = [
        'country_code',
        'mobile_number',
        'otp',
        'expires_at',
        'is_verified',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }
}
