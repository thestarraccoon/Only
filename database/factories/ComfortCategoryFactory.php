<?php

namespace Database\Factories;

use App\Models\ComfortCategory;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComfortCategoryFactory extends Factory
{
    protected $model = ComfortCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Премиум', 'Бизнес', 'Стандарт']),
            'level' => fake()->numberBetween(1, 3),
            'description' => fake()->sentence(),
        ];
    }
}
