<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use Hash;
use Spatie\LaravelData\Data;

final class RegisterDTO extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
        $this->password = Hash::make($password);
    }

    /**
     * @return array<string, string>
     * */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
