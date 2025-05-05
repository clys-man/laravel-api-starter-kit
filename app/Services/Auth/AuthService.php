<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Models\User;
use App\Services\User\UserServiceInterface;
use Auth;
use Laravel\Sanctum\NewAccessToken;
use Throwable;

final readonly class AuthService implements AuthServiceInterface
{
    public function __construct(
        private UserServiceInterface $userService,
    ) {}

    /**
     * @throws Throwable
     */
    public function register(RegisterDTO $data): User
    {
        return $this->userService->create(
            data: $data
        );
    }

    public function login(LoginDTO $data, bool $remember = false): ?User
    {
        if (! Auth::attempt([
            'email' => $data->email,
            'password' => $data->password,
        ], $remember)) {
            return null;
        }

        /** @var User|null $user */
        $user = Auth::user();

        return $user;
    }

    /**
     * @param  array<string>  $abilities
     */
    public function createToken(
        User $user,
        string $name = 'token',
        array $abilities = ['*']
    ): NewAccessToken {
        return $user->createToken(
            name: $name,
            abilities: $abilities
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
