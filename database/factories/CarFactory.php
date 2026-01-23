<?php

namespace Database\Factories;

use App\Models\ComfortCategory;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    protected $model = \App\Models\Car::class;

    public function definition(): array
    {
        return [
            'car_model_id' => \App\Models\CarModel::factory(),
            'driver_id' => \App\Models\Driver::factory(),
            'license_plate' => strtoupper(fake()->bothify('?###??###')),
            'year' => fake()->numberBetween(2018, 2024),
            'color' => fake()->colorName(),
            'is_active' => true,
        ];
    }
}
