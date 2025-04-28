<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
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
        protected readonly DatabaseManager $database
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
        return $this->database->transaction(
            fn () => $this->model->newQuery()->create($attributes),
            attempts: 3
        );
    }

    /**
     * @return TEntity
     */
    public function update(int|string $id, Data $data): Model
    {
        /** @var TEntity */
        return $this->database->transaction(function () use ($id, $data): Model {
            /** @var array<string, mixed> */
            $attributes = $data->toArray();

            $model = $this->findById($id);

            if ( ! $model instanceof Model) {
                throw new Exception('Entity not found.');
            }

            $model->update($attributes);

            return $model;
        }, attempts: 3);
    }

    public function delete(int|string $id): ?bool
    {
        return $this->database->transaction(function () use ($id) {
            $model = $this->findById($id);

            if ( ! $model instanceof Model) {
                return null;
            }

            return $model->delete();
        }, attempts: 3);
    }
}
