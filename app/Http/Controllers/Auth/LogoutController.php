<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class LogoutController
{
    public function __invoke(
        Request $request,
        AuthService $service
    ): Response {
        $service->logout();

        return new JsonResponse(
            data: null,
            status: Response::HTTP_NO_CONTENT,
        );
    }
}
