<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DTO\User\NewUserDTO;
use App\Models\User;
use App\Repositories\User\UserRepository;
use Throwable;

final readonly class AuthService
{
    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * @param NewUserDTO $payload
     * @return User
     * @throws Throwable
     */
    public function register(NewUserDTO $payload): User
    {
        return $this->userRepository->create(
            payload: $payload,
        );
    }
}
