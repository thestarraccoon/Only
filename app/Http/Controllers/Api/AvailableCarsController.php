<?php

namespace App\Http\Controllers\Api;

use App\DTO\AvailableCarsDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\AvailableCarsRequest;
use App\Http\Resources\CarListResource;
use App\Services\AvailableCarsService;
use Illuminate\Http\JsonResponse;

class AvailableCarsController extends Controller
{
    public function __construct(
        private readonly AvailableCarsService $availableCarsService
    ) {
    }

    public function __invoke(AvailableCarsRequest $request): JsonResponse
    {
        $dto = AvailableCarsDto::fromRequest($request);

        $cars = $this->availableCarsService->getAvailableCarsForUser(
            $request->user(),
            $dto
        );

        return (new CarListResource($cars))->response();
    }
}
