<?php

declare(strict_types=1);

namespace App\Services\User;

use App\DTO\Auth\RegisterDTO;
use App\DTO\User\UserDTO;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Throwable;

interface UserServiceInterface
{
    /**
     * @return Collection<int, User>|LengthAwarePaginator<int, User>
     *
     * @throws Throwable
     */
    public function getAll(bool $paginated = false): Collection|LengthAwarePaginator;

    /**
     * @throws Throwable
     */
    public function find(string $id): User;

    /**
     * @throws Throwable
     */
    public function create(RegisterDTO $data): User;

    /**
     * @throws Throwable
     */
    public function update(string $id, UserDTO $data): User;

    /**
     * @throws Throwable
     */
    public function delete(string $id): void;
}
