<?php

namespace Database\Factories;

use App\Models\CarModel;
use App\Models\ComfortCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarModelFactory extends Factory
{
    protected $model = CarModel::class;

    public function definition(): array
    {
        return [
            'brand' => $this->faker->randomElement([
                'Toyota', 'Honda', 'BMW', 'Mercedes', 'Audi', 'Tesla', 'Volkswagen'
            ]),
            'model' => $this->faker->randomElement([
                'Camry', 'Accord', 'M5', 'S-Class', 'A8', 'Model S', 'Passat'
            ]),
            'comfort_category_id' => ComfortCategory::factory(),
        ];
    }

    public function bmwM5(): static
    {
        return $this->state(fn (array $attributes) => [
            'brand' => 'BMW',
            'model' => 'M5',
        ]);
    }

    public function standard(): static
    {
        return $this->state(fn (array $attributes) => [
            'comfort_category_id' => 3,
        ]);
    }
}
