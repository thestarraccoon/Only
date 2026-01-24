<?php
// tests/Feature/IntegrationTest.php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test Полный путь: register → available-cars */
    public function test_complete_user_journey(): void
    {
        // 1. Регистрация
        $registerResponse = $this->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/auth/register', [
                'name' => 'Иван Директор',
                'email' => 'journey@test.com',
                'password' => '123qweasd',
                'password_confirmation' => '123qweasd'
            ]);

        $registerResponse->assertStatus(201);
        $token = $registerResponse->json('data.access_token');
        $userId = $registerResponse->json('data.user.id');

        // 2. /me
        $meResponse = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'X-Corporate-ID' => 'corp-dir-001'
        ])->getJson('/api/auth/me');

        $meResponse->assertStatus(200);

        // 3. available-cars (15 машин)
        $carsResponse = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'X-Corporate-ID' => 'corp-dir-001'
        ])->postJson('/api/available-cars', [
            'start_at' => '2026-01-25 10:00:00',
            'end_at' => '2026-01-25 12:00:00'
        ]);

        $carsResponse->assertStatus(200)
            ->assertJsonPath('meta.total', 15);
    }
}
