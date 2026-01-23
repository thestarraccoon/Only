<?php

namespace App\Services;

use App\DTO\AvailableCarsDto;
use App\Models\User;
use App\Repositories\CarRepository;
use Illuminate\Database\Eloquent\Collection;

class AvailableCarsService
{
    public function __construct(
        private readonly CarRepository $carRepository
    ) {
    }

    public function getAvailableCarsForUser(User $user, AvailableCarsDto $dto): Collection
    {
        $allowedComfortCategoryIds = $this->getAllowedComfortCategoryIds($user);

        if (empty($allowedComfortCategoryIds)) {
            return new Collection();
        }

        return $this->carRepository->getAvailableCars($allowedComfortCategoryIds, $dto);
    }

    private function getAllowedComfortCategoryIds(User $user): array
    {
        if (!$user->position) {
            return [];
        }

        return $user->position
            ->comfortCategories()
            ->pluck('comfort_categories.id')
            ->toArray();
    }
}
