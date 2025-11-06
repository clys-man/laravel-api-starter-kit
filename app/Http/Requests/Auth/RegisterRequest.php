<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\DTO\Auth\RegisterDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class RegisterRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|list<string>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function toDTO(): RegisterDTO
    {
        return RegisterDTO::from($this->validated());
    }
}
