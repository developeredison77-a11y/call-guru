<?php

namespace App\Services\Backend;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ContentManagementService
{
    /**
     * @param  class-string<Model>  $model
     */
    public function list(string $model): Collection
    {
        return $model::query()->latest()->get();
    }

    /**
     * @param  class-string<Model>  $model
     */
    public function find(string $model, int $id): Model
    {
        return $model::query()->findOrFail($id);
    }

    /**
     * @param  class-string<Model>  $model
     * @param  array<string, mixed>  $attributes
     */
    public function create(string $model, array $attributes): Model
    {
        return $model::query()->create($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Model $record, array $attributes): Model
    {
        $record->update($attributes);

        return $record->refresh();
    }

    public function toggleStatus(Model $record): Model
    {
        $record->update(['status' => (int) ! $record->status]);

        return $record->refresh();
    }

    public function softDelete(Model $record): void
    {
        $record->delete();
    }
}
