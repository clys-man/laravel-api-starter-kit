<?php

declare(strict_types=1);

namespace App\DTO\User;

use Spatie\LaravelData\Data;

final class UserDTO extends Data
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}
}
