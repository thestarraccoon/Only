<?php

namespace Database\Factories;

use App\Models\ComfortCategory;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarModelFactory extends Factory
{
    protected $model = \App\Models\CarModel::class;

    public function definition(): array
    {
        return [
            'brand' => fake()->randomElement(['Mercedes-Benz', 'BMW', 'Toyota', 'Audi', 'Lexus']),
            'model' => fake()->randomElement(['S-Class', '5 Series', 'Camry', 'A6', 'ES']),
            'comfort_category_id' => ComfortCategory::factory(),
        ];
    }
}
