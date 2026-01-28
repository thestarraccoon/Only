<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Route;

class MiddlewareTest extends TestCase
{
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
            ->assertJsonPath('error.message', __('api/errors.access_forbidden'));
    }

    public function test_api_generic_exception_triggers_server_error_method()
    {
        Route::get('/api/boom', fn() => throw new \RuntimeException('BOOM!'));

        $response = $this->getJson('/api/boom');

        $response
            ->assertStatus(500)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'INTERNAL_SERVER_ERROR');
    }

    /** @test */
    public function test_non_api_requests_return_null_from_api_handler()
    {
        $response = $this->get('/non-api/route');
        $response->assertStatus(404);
    }

    /** @test */
    public function test_api_not_found_triggers_not_found_method()
    {
        $response = $this->getJson('/api/does-not-exist');

        $response
            ->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'RESOURCE_NOT_FOUND');
    }

    /** @test */
    public function test_api_auth_me_without_token_triggers_unauthorized_method()
    {
        $response = $this->getJson('/api/auth/me');

        $response
            ->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'AUTH_UNAUTHORIZED')
            ->assertJsonPath('error.status', 401);
    }

    /** @test */
    public function test_api_available_cars_without_corporate_id_triggers_forbidden_method()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/available-cars');

        $response
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'ACCESS_FORBIDDEN');
    }

    /** @test */
    public function test_api_validation_error_triggers_validation_error_method()
    {
        $response = $this->postJson('/api/auth/login', []);

        $response
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonPath('error.status', 422)
            ->assertJsonStructure([
                'success',
                'error' => ['code', 'message', 'status', 'request_id', 'timestamp', 'details']
            ]);
    }
}
