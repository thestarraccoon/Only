<?php
// tests/Feature/Api/MiddlewareTest.php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test Middleware требует X-Corporate-ID */
    public function test_corporate_role_middleware_requires_header(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/available-cars', [
                'start_at' => '2026-01-25 10:00:00',
                'end_at' => '2026-01-25 12:00:00'
            ]);

        $response->assertStatus(403);
    }

    /** @test Локализация ошибок */
    public function test_error_localization(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->withHeaders(['Accept-Language' => 'ru'])
            ->postJson('/api/available-cars', [
                'start_at' => '2026-01-25 10:00:00',
                'end_at' => '2026-01-25 12:00:00'
            ]);

        $response->assertStatus(403)
            ->assertJsonPath('error.message', 'X-Corporate-ID header required');
    }
}
