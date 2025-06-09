<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
     * @return LengthAwarePaginator<int, TEntity>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

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
