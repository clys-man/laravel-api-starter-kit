<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class RegisterController
{
    public function __invoke(
        RegisterRequest $request,
        AuthService $service
    ): Response {
        $user = $service->register(
            data: $request->toDTO()
        );

        $token = $user->createToken(
            name: 'API Access Token',
            abilities: ['*']
        );

        return new JsonResponse([
            'user' => new UserResource(resource: $user),
            'token' => $token->plainTextToken,
        ], Response::HTTP_CREATED);
    }
}
