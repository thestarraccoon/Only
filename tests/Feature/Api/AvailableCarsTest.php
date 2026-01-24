<?php
// tests/Feature/Api/AvailableCarsTest.php

namespace Tests\Feature\Api;

use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailableCarsTest extends TestCase
{
    use RefreshDatabase;

    /** @test Директор видит все 15 машин */
    public function test_director_sees_all_cars(): void
    {
        $director = User::factory()->create(['position_id' => 1]);

        $response = $this->actingAs($director, 'sanctum')
            ->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/available-cars', [
                'start_at' => '2026-01-25 10:00:00',
                'end_at' => '2026-01-25 12:00:00'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 15); // Все активные машины
    }

    /** @test Специалист видит только Стандарт (6 машин) */
    public function test_specialist_sees_standard_only(): void
    {
        $specialist = User::factory()->create(['position_id' => 3]);

        $response = $this->actingAs($specialist, 'sanctum')
            ->withHeaders(['X-Corporate-ID' => 'corp-spec-001'])
            ->postJson('/api/available-cars', [
                'start_at' => '2026-01-25 10:00:00',
                'end_at' => '2026-01-25 12:00:00'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 6); // Только comfort_level=1
    }

    /** @test Бронь исключает машину */
    public function test_booking_excludes_car(): void
    {
        $director = User::factory()->create(['position_id' => 1]);
        $car = Car::factory()->create(['is_active' => true]);

        Booking::factory()->create([
            'car_id' => $car->id,
            'start_at' => '2026-01-25 09:00:00',
            'end_at' => '2026-01-25 11:00:00',
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($director, 'sanctum')
            ->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/available-cars', [
                'start_at' => '2026-01-25 10:00:00', // Пересечение!
                'end_at' => '2026-01-25 12:00:00'
            ]);

        $response->assertStatus(200)
            ->assertJsonMissing([
                'id' => $car->id
            ]);
    }

    /** @test Без X-Corporate-ID = 403 */
    public function test_no_corporate_id_forbidden(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/available-cars', [
                'start_at' => '2026-01-25 10:00:00',
                'end_at' => '2026-01-25 12:00:00'
            ]);

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'ACCESS_FORBIDDEN');
    }

    /** @test Валидация дат */
    public function test_invalid_dates_validation(): void
    {
        $director = User::factory()->create(['position_id' => 1]);

        $response = $this->actingAs($director, 'sanctum')
            ->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/available-cars', [
                'start_at' => 'invalid-date',
                'end_at' => '2026-01-25 12:00:00'
            ]);

        $response->assertStatus(422);
    }

    /** @test end_at раньше start_at */
    public function test_end_before_start_validation(): void
    {
        $director = User::factory()->create(['position_id' => 1]);

        $response = $this->actingAs($director, 'sanctum')
            ->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/available-cars', [
                'start_at' => '2026-01-25 12:00:00',
                'end_at' => '2026-01-25 10:00:00' // ❌
            ]);

        $response->assertStatus(422);
    }

    /** @test Фильтрация по car_model_id */
    public function test_filter_by_car_model(): void
    {
        $director = User::factory()->create(['position_id' => 1]);

        $response = $this->actingAs($director, 'sanctum')
            ->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/available-cars', [
                'start_at' => '2026-01-25 10:00:00',
                'end_at' => '2026-01-25 12:00:00',
                'car_model_id' => 3 // BMW M5
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1); // Только активная BMW M5
    }
}
