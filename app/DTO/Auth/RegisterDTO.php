<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use Spatie\LaravelData\Data;

final class RegisterDTO extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
        $this->password = bcrypt($password);
    }
}
