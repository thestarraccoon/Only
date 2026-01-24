<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\RegisterUserService;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request, RegisterUserService $service): JsonResponse
    {
        $corporateId = $request->header('X-Corporate-ID');

        $user = $service->register($request->validated(), $corporateId);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => __('auth.register_success'),
            'data' => [
                'user' => new UserResource($user->load(['position', 'roles'])),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }
}
