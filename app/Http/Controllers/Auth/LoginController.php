<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

final readonly class LoginController
{
    public function __construct(
        private AuthService $service,
    ) {}

    /**
     * @throws ValidationException
     */
    public function __invoke(LoginRequest $request): Response
    {
        dd($request->toDTO());

        $user = $this->service->login(
            data: $request->toDTO()
        );

        if (! $user instanceof User) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $token = $this->service->createToken($user);

        return new JsonResponse([
            'token' => $token->plainTextToken,
        ]);
    }
}
