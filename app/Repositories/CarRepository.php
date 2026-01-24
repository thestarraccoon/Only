<?php

namespace App\Repositories;

use App\DTO\AvailableCarsDto;
use App\Models\Car;
use Illuminate\Database\Eloquent\Collection;

class CarRepository
{
    public function getAvailableCars(
        array $allowedComfortCategoryIds,
        AvailableCarsDto $dto
    ): Collection {

        $timeOverlap = function ($q) use ($dto) {
            return $q->whereBetween('start_at', [$dto->startAt, $dto->endAt])
                ->orWhereBetween('end_at', [$dto->startAt, $dto->endAt])
                ->orWhere(function ($q2) use ($dto) {
                    $q2->where('start_at', '<=', $dto->startAt)
                        ->where('end_at', '>=', $dto->endAt);
                });
        };

        $query = Car::query()
            ->with([
                'carModel.comfortCategory',
                'driver',
            ])
            ->where('is_active', true)
            ->whereHas('carModel', function ($query) use ($allowedComfortCategoryIds) {
                $query->whereIn('comfort_category_id', $allowedComfortCategoryIds);
            })
            ->whereDoesntHave('bookings', function ($q) use ($timeOverlap) {
                $q->active()->overlaps($timeOverlap);
            })
            ->whereDoesntHave('driver.cars.bookings', function ($q) use ($timeOverlap) {
                $q->active()->overlaps($timeOverlap);
            });

        if ($dto->carModelId) {
            $query->where('car_model_id', $dto->carModelId);
        }

        if ($dto->comfortCategoryId) {
            $query->whereHas('carModel', function ($q) use ($dto) {
                $q->where('comfort_category_id', $dto->comfortCategoryId);
            });
        }

        return $query->get();
    }
}
