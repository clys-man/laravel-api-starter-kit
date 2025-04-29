<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
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
     * @return LengthAwarePaginator<int, TEntity>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, TEntity> */
        return $this->model->newQuery()->paginate($perPage);
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
        /** @var array<string, mixed> */
        $attributes = $data->toArray();

        /** @var TEntity */
        return DB::transaction(
            fn () => $this->model->newQuery()->create($attributes),
            3
        );
    }

    /**
     * @return TEntity
     */
    public function update(int|string $id, Data $data): Model
    {
        /** @var TEntity */
        return DB::transaction(function () use ($id, $data): Model {
            /** @var array<string, mixed> */
            $attributes = $data->toArray();

            $model = $this->findById($id);

            if ( ! $model instanceof Model) {
                throw new ModelNotFoundException();
            }

            $model->update($attributes);

            return $model;
        }, 3);
    }

    public function delete(int|string $id): ?bool
    {
        return DB::transaction(function () use ($id) {
            $model = $this->findById($id);

            if ( ! $model instanceof Model) {
                throw new ModelNotFoundException();
            }

            return $model->delete();
        }, 3);
    }
}
