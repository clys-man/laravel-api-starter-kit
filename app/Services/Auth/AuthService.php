<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DTO\User\NewUserDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Throwable;

final readonly class AuthService
{
    /**
     * @param  UserRepositoryInterface<User>  $userRepository
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @throws Throwable
     */
    public function register(NewUserDTO $data): User
    {
        return $this->userRepository->create(
            data: $data
        );
    }
}
