<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

/**
 * @template TEntity of Model
 */
interface RepositoryInterface
{
    /**
     * @return Collection<int, TEntity>
     */
    public function findAll(): Collection;

    /**
     * @return Paginator<int, TEntity>
     */
    public function paginate(int $perPage = 15): Paginator;

    /**
     * @return TEntity|null
     */
    public function findById(int|string $id); // @pest-ignore-type

    /**
     * @return TEntity
     */
    public function create(Data $data); // @pest-ignore-type

    /**
     * @return TEntity
     */
    public function update(int|string $id, Data $data); // @pest-ignore-type

    public function delete(int|string $id): ?bool;
}
