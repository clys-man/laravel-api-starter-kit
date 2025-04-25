<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\DTO\User\NewUserDTO;
use App\Models\User;
use Illuminate\Database\DatabaseManager;

final class UserRepository
{
    public function __construct(
        protected readonly User $model,
        protected readonly DatabaseManager $database
    ) {}

    public function create(NewUserDTO $payload): User
    {
        return $this->database->transaction(
            callback: fn() => User::query()->create(
                attributes: $payload->toArray()
            ),
            attempts: 3,
        );
    }
}
