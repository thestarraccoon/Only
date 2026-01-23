<?php

namespace Database\Factories;

use App\Models\ComfortCategory;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = \App\Models\Driver::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->phoneNumber(),
            'license_number' => fake()->numerify('#### ######'),
        ];
    }
}
