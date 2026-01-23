<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;
use App\Models\Car;
use App\Models\User;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $startAt = fake()->dateTimeBetween('now', '+1 month');
        $endAt = (clone $startAt)->modify('+' . fake()->numberBetween(2, 8) . ' hours');

        return [
            'car_id' => Car::factory(),
            'user_id' => User::factory(),
            'start_at' => $startAt,
            'end_at' => $endAt,
            'destination' => fake()->city(),
            'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
        ];
    }
}
