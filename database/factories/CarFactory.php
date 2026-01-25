<?php
// database/factories/CarFactory.php

namespace Database\Factories;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        return [
            'car_model_id' => CarModel::factory(),
            'driver_id' => User::factory(),
            'license_plate' => $this->faker->regexify('[A-Z]{1}\d{3}[A-Z]{2}\d{2,3}'),
            'year' => $this->faker->year(2018, 2026),
            'color' => $this->faker->randomElement(['black', 'white', 'silver', 'gray', 'blue', 'red']),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function bmw(): static
    {
        return $this->state(fn (array $attributes) => [
            'car_model_id' => 3,
        ]);
    }
}
