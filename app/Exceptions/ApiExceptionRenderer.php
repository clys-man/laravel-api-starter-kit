<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final readonly class ApiExceptionRenderer
{
    public function __construct(
        private Throwable $exception,
    ) {}

    public function render(): JsonResponse
    {
        $status = $this->getStatusCode($this->exception);
        $title = $this->getTitle($status);
        $detail = $this->getDetail($this->exception);

        $response = [
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
        ];

        if ($this->exception instanceof ValidationException) {
            $response['errors'] = $this->exception->errors();
        }

        if (app()->isLocal()) {
            $response['exception'] = $this->exception::class;
            $response['trace'] = $this->exception->getTrace();
        }

        return new JsonResponse(
            data: $response,
            status: $status,
            headers: [
                'Content-Type' => 'application/problem+json',
            ],
        );
    }

    private function getStatusCode(Throwable $e): int
    {
        return match (true) {
            $e instanceof BadRequestException => 400,
            $e instanceof AuthenticationException => 401,
            $e instanceof AuthorizationException => 403,
            $e instanceof ValidationException => 422,
            $e instanceof NotFoundHttpException => 404,
            default => 500,
        };
    }

    private function getTitle(int $status): string
    {
        return match ($status) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            default => 'Internal Server Error',
        };
    }

    private function getDetail(Throwable $e): string
    {
        return in_array($e->getMessage(), ['', '0'], true) ? 'An unexpected error occurred.' : $e->getMessage();
    }
}
