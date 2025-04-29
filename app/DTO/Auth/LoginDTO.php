<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use Spatie\LaravelData\Data;

final class LoginDTO extends Data
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
