<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;
use Symfony\Component\HttpFoundation\Response;

final readonly class RegisterController
{
    public function __invoke(RegisterRequest $request): Response
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        /** @var NewAccessToken $token */
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
