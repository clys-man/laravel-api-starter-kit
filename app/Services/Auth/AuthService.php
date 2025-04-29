<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Auth;
use Laravel\Sanctum\NewAccessToken;
use RuntimeException;
use Throwable;

final readonly class AuthService implements AuthServiceInterface
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
    public function register(RegisterDTO $data): User
    {
        return $this->userRepository->create(
            data: $data
        );
    }

    /**
     * @throws Throwable
     */
    public function login(LoginDTO $data, bool $remember = false): ?NewAccessToken
    {
        if (! Auth::attempt(['email' => $data->email, 'password' => $data->password], $remember)) {
            throw new RuntimeException('Authentication failed.');
        }

        return Auth::user()?->createToken(
            name: 'API Access Token',
            abilities: ['*']
        );
    }

    /**
     * @throws Throwable
     */
    public function logout(): void
    {
        Auth::user()?->currentAccessToken()->delete();
    }
}
