<?php

namespace App\Repositories\Auth;

use App\Models\User;

class UserRepository
{
    public function findByMobileNumber(string $countryCode, string $mobileNumber): ?User
    {
        return User::query()
            ->where('country_code', $countryCode)
            ->where('mobile_number', $mobileNumber)
            ->first();
    }

    public function existsByMobileNumber(string $countryCode, string $mobileNumber): bool
    {
        return User::query()
            ->where('country_code', $countryCode)
            ->where('mobile_number', $mobileNumber)
            ->exists();
    }

    public function create(array $attributes): User
    {
        return User::query()->create($attributes);
    }
}
