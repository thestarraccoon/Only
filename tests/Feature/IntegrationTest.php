<?php
// tests/Feature/IntegrationTest.php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    /** Helper для будущих дат */
    private function futureDateRange(): array
    {
        $startAt = now()->addDays(7)->setHour(10)->setMinute(0)->setSecond(0);
        $endAt = $startAt->copy()->addHours(2);

        return [
            'start_at' => $startAt->format('Y-m-d H:i:s'),
            'end_at' => $endAt->format('Y-m-d H:i:s')
        ];
    }

    /** @test Полный путь: register → available-cars */
    public function test_complete_user_journey(): void
    {
        $dates = $this->futureDateRange();

        $uniqueEmail = 'integration_test_' . uniqid('', true) . '@example.com';

        $registerResponse = $this->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/auth/register', [
                'name' => 'Интеграционный Директор',
                'email' => $uniqueEmail,
                'password' => '123qweasd',
                'password_confirmation' => '123qweasd'
            ]);

        $registerResponse->assertStatus(201);
        $token = $registerResponse->json('data.access_token');

        $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'X-Corporate-ID' => 'corp-dir-001'
        ])
            ->getJson('/api/auth/me')
            ->assertStatus(200);

        $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'X-Corporate-ID' => 'corp-dir-001'
        ])
            ->postJson('/api/available-cars', $dates)
            ->assertStatus(200);
    }
}
