<?php

declare(strict_types=1);

namespace App\Repositories;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

/**
 * @implements RepositoriesRepositoryInterface<Model>
 */
abstract class EloquentRepository implements RepositoryInterface
{
    public function __construct(
        protected readonly Model $model,
    ) {}

    /**
     * @return Collection<int, Model>
     */
    public function findAll(): Collection
    {
        return $this->model->newQuery()->get();
    }

    /**
     * @return Paginator<int, Model>
     */
    public function paginate(int $perPage = 15): Paginator
    {
        return $this->model->newQuery()->simplePaginate($perPage);
    }

    public function findById(int|string $id): ?Model
    {
        return $this->model->newQuery()->find($id);
    }

    public function create(Data $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            /** @var array<string, mixed> $attributes */
            $attributes = $data->toArray();

            return $this->model->newQuery()->create($attributes);
        }, 3);
    }

    public function update(int|string $id, Data $data): Model
    {
        return DB::transaction(function () use ($id, $data): Model {
            /** @var array<string, mixed> $attributes */
            $attributes = $data->toArray();

            $model = $this->findById($id);

            if (! $model instanceof Model) {
                throw new ModelNotFoundException();
            }

            $model->update($attributes);

            return $model;
        }, 3);
    }

    public function delete(int|string $id): ?bool
    {
        return DB::transaction(function () use ($id): ?bool {
            $model = $this->findById($id);

            if (! $model instanceof Model) {
                throw new ModelNotFoundException();
            }

            return $model->delete();
        }, 3);
    }
}
