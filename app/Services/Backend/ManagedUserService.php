<?php

namespace App\Services\Backend;

use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ManagedUserService
{
    public function list(UserTypeEnum $type): Collection
    {
        return $this->query($type)
            ->latest()
            ->get();
    }

    public function find(int $id, UserTypeEnum $type): User
    {
        return $this->query($type)->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(User $user, array $attributes): User
    {
        $user->update($attributes);

        return $user->refresh();
    }

    public function toggleStatus(User $user): User
    {
        $nextStatus = (int) $user->status === UserStatusEnum::Active->value
            ? UserStatusEnum::Inactive
            : UserStatusEnum::Active;

        $user->update(['status' => $nextStatus->value]);

        return $user->refresh();
    }

    public function softDelete(User $user): void
    {
        $user->delete();
    }

    private function query(UserTypeEnum $type)
    {
        return User::query()->where('type', $type->value);
    }
}
