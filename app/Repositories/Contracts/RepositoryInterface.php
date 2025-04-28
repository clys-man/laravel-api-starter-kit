<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

/**
 * @template TEntity
 */
interface RepositoryInterface
{
    /**
     * Retorna todos os registros.
     *
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
    public function findById(int|string $id);

    /**
     * @return TEntity
     */
    public function create(Data $data);

    /**
     * @return TEntity
     */
    public function update(int|string $id, Data $data);

    public function delete(int|string $id): ?bool;
}
