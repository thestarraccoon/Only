<?php
// database/factories/UserFactory.php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function director(): static
    {
        return $this->state(fn (array $attributes) => [
            'position_id' => 1, // Директор
        ]);
    }

    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'position_id' => 2, // Топ-менеджер
        ]);
    }

    public function specialist(): static
    {
        return $this->state(fn (array $attributes) => [
            'position_id' => 3, // Специалист
        ]);
    }
}
