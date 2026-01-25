<?php
// database/factories/ComfortCategoryFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ComfortCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Стандарт', 'Комфорт', 'Премиум']),
            'level' => $this->faker->numberBetween(1, 4),
            'description' => $this->faker->optional()->sentence(),
        ];
    }

    public function standard(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Стандарт',
            'level' => 3,
        ]);
    }

    public function comfort(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Комфорт',
            'level' => 2,
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Премиум',
            'level' => 1,
        ]);
    }
}
