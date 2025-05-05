<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Models\User;
use Laravel\Sanctum\NewAccessToken;
use Throwable;

interface AuthServiceInterface
{
    /**
     * @throws Throwable
     */
    public function register(RegisterDTO $data): User;

    /**
     * @throws Throwable
     */
    public function login(
        LoginDTO $data,
        bool $remember = false
    ): ?User;

    /**
     * @param  array<string>  $abilities
     */
    public function createToken(
        User $user,
        string $name,
        array $abilities = ['*']
    ): NewAccessToken;

    /**
     * @throws Throwable
     */
    public function logout(): void;
}
