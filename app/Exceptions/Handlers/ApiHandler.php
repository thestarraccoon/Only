<?php

namespace App\Exceptions\Handlers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class ApiHandler
{
    public function handle(Request $request, \Throwable $e): ?JsonResponse
    {
        if (!$request->is('api/*')) {
            return null;
        }

        $requestId = (string) Str::uuid();

        return match (true) {
            $e instanceof NotFoundHttpException => $this->notFound($requestId),
            $e instanceof AuthenticationException => $this->unauthorized($requestId),
            $e instanceof ValidationException => $this->validationError($e, $requestId),
            default => $this->serverError($requestId)
        };
    }

    private function unauthorized(string $requestId): JsonResponse
    {
        return $this->errorResponse(
            code: 'AUTH_UNAUTHORIZED',
            message: __('api/errors.auth.unauthorized'),
            status: 401,
            requestId: $requestId
        );
    }

    private function notFound(string $requestId): JsonResponse
    {
        return $this->errorResponse(
            code: 'RESOURCE_NOT_FOUND',
            message: __('api/errors.not_found'),
            status: 404,
            requestId: $requestId
        );
    }

    private function serverError(string $requestId): JsonResponse
    {
        return $this->errorResponse(
            code: 'INTERNAL_SERVER_ERROR',
            message: __('api/errors.server_error'),
            status: 500,
            requestId: $requestId
        );
    }

    private function validationError(ValidationException $e, string $requestId): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => __('api/errors.validation'),
                'status' => 422,
                'request_id' => $requestId,
                'timestamp' => now()->toISOString(),
                'details' => $e->errors(),
            ]
        ], 422);
    }

    private function errorResponse(
        string $code,
        string $message,
        int $status,
        string $requestId,
        array $debug = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'status' => $status,
                'request_id' => $requestId,
                'timestamp' => now()->toISOString(),
            ]
        ];

        if (!empty($debug)) {
            $response['error']['debug'] = $debug;
        }

        return response()->json($response, $status);
    }
}
