<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Data;

/**
 * @template TEntity of Model
 *
 * @implements RepositoryInterface<TEntity>
 */
abstract class EloquentRepository implements RepositoryInterface
{
    /**
     * @param  TEntity  $model
     */
    public function __construct(
        protected readonly Model $model,
    ) {}

    /**
     * @return Collection<int, TEntity>
     */
    public function findAll(): Collection
    {
        /** @var Collection<int, TEntity> */
        return $this->model->newQuery()->get();
    }

    /**
     * @return Paginator<int, TEntity>
     */
    public function paginate(int $perPage = 15): Paginator
    {
        /** @var Paginator<int, TEntity> */
        return $this->model->newQuery()->simplePaginate($perPage);
    }

    /**
     * @return TEntity|null
     */
    public function findById(int|string $id): ?Model
    {
        /** @var TEntity|null */
        return $this->model->newQuery()->find($id);
    }

    /**
     * @return TEntity
     */
    public function create(Data $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            /** @var array<string, mixed> $attributes */
            $attributes = $data->toArray();

            /** @var TEntity */
            return $this->model->newQuery()->create($attributes);
        }, 3);
    }

    /**
     * @return TEntity
     */
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
