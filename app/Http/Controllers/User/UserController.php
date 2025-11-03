<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\DTO\User\UserDTO;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Services\User\UserService;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class UserController
{
    public function __construct(
        private UserService $service,
    ) {}

    public function index(Request $request): Responsable
    {
        $perPage = $request->integer('per_page', default: 15);
        $users = $this->service->paginate($perPage);

        return UserResource::collection($users);
    }

    public function show(string $id): JsonResponse
    {
        $user = $this->service->find($id);

        return new JsonResponse(
            new UserResource($user),
            Response::HTTP_OK
        );
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = $this->service->update(
            $id,
            UserDTO::from($request->validated())
        );

        return new JsonResponse(
            new UserResource($user),
            Response::HTTP_OK
        );
    }

    public function destroy(string $id): Response
    {
        $this->service->delete($id);

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT
        );
    }
}
