<?php

namespace App\DTO;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AvailableCarsDto
{
    public function __construct(
        public readonly Carbon $startAt,
        public readonly Carbon $endAt,
        public readonly ?int $carModelId = null,
        public readonly ?int $comfortCategoryId = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            startAt: Carbon::parse($data['start_at']),
            endAt: Carbon::parse($data['end_at']),
            carModelId: $data['car_model_id'] ?? null,
            comfortCategoryId: $data['comfort_category_id'] ?? null,
        );
    }

    public static function fromRequest(FormRequest $request): self
    {
        return self::fromArray($request->validated());
    }
}
