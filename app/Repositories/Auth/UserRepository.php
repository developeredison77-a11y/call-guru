<?php

namespace App\Repositories\Auth;

use App\Models\User;

class UserRepository
{
    public function findByMobileNumber(string $mobileNumber): ?User
    {
        return User::query()
            ->where('mobile_number', $mobileNumber)
            ->first();
    }

    public function existsByMobileNumber(string $mobileNumber): bool
    {
        return User::query()
            ->where('mobile_number', $mobileNumber)
            ->exists();
    }

    public function create(array $attributes): User
    {
        return User::query()->create($attributes);
    }
}
