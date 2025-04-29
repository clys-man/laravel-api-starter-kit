<?php

declare(strict_types=1);

namespace App\Services\User;

use App\DTO\Auth\RegisterDTO;
use App\DTO\User\UserDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Throwable;

final readonly class UserService implements UserServiceInterface
{
    /**
     * @param  UserRepositoryInterface<User>  $userRepository
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @return Collection<int, User>|LengthAwarePaginator<int, User>
     *
     * @throws Throwable
     */
    public function getAll(bool $paginated = false, int $perPage = 15): Collection|LengthAwarePaginator
    {
        return $paginated
            ? $this->userRepository->paginate($perPage)
            : $this->userRepository->findAll();
    }

    /**
     * @throws Throwable
     */
    public function find(string $id): User
    {
        $user = $this->userRepository->findById($id);

        if ( ! $user instanceof User) {
            throw new ModelNotFoundException();
        }

        return $user;
    }

    /**
     * @throws Throwable
     */
    public function create(RegisterDTO $data): User
    {
        return $this->userRepository->create($data);
    }

    /**
     * @throws Throwable
     */
    public function update(string $id, UserDTO $data): User
    {
        return $this->userRepository->update($id, $data);
    }

    /**
     * @throws Throwable
     */
    public function delete(string $id): void
    {
        $this->userRepository->delete($id);
    }
}
