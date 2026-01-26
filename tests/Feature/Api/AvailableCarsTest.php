<?php

namespace Tests\Feature\Api;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailableCarsTest extends TestCase
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

    /** @test Директор видит все 15 машин */
    public function test_director_sees_all_cars(): void
    {
        $director = User::factory()->create(['position_id' => 1]);
        $dates = $this->futureDateRange();

        $response = $this->actingAs($director, 'sanctum')
            ->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/available-cars', $dates);

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 15);
    }

    /** @test Специалист видит только Стандарт (6 машин) */
    public function test_specialist_sees_standard_only(): void
    {
        $specialist = User::factory()->create(['position_id' => 3]);
        $dates = $this->futureDateRange();

        $response = $this->actingAs($specialist, 'sanctum')
            ->withHeaders(['X-Corporate-ID' => 'corp-spec-001'])
            ->postJson('/api/available-cars', $dates);

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 5);
    }

    /** @test Бронь исключает машину */
    public function test_booking_excludes_car(): void
    {
        $dates = $this->futureDateRange();

        $bookingStart = $dates['start_at'];
        $bookingEnd = (now()->addDays(7)->setHour(11)->setMinute(0)->setSecond(0))
            ->format('Y-m-d H:i:s');

        $director = User::where('position_id', 1)->first();
        $this->assertNotNull($director);

        $car = Car::first();
        $this->assertNotNull($car);

        Booking::create([
            'car_id' => $car->id,
            'user_id' => $director->id,
            'start_at' => $bookingStart,
            'end_at' => $bookingEnd,
            'status' => BookingStatus::CONFIRMED->value,
        ]);

        $response = $this->actingAs($director, 'sanctum')
            ->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/available-cars', $dates);

        $response->assertStatus(200);

        $carIds = collect($response->json('data'))->pluck('id')->toArray();
        $this->assertNotContains($car->id, $carIds);
    }

    /** @test Без X-Corporate-ID = 403 */
    public function test_no_corporate_id_forbidden(): void
    {
        $user = User::factory()->create();
        $dates = $this->futureDateRange();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/available-cars', $dates);

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'ACCESS_FORBIDDEN');
    }

    /** @test Валидация дат */
    public function test_invalid_dates_validation(): void
    {
        $director = User::factory()->create(['position_id' => 1]);
        $dates = $this->futureDateRange();

        $response = $this->actingAs($director, 'sanctum')
            ->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/available-cars', [
                'start_at' => 'invalid-date',
                'end_at' => $dates['end_at']
            ]);

        $response->assertStatus(422);
    }

    /** @test end_at раньше start_at */
    public function test_end_before_start_validation(): void
    {
        $director = User::factory()->create(['position_id' => 1]);
        $dates = $this->futureDateRange();

        $response = $this->actingAs($director, 'sanctum')
            ->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/available-cars', [
                'start_at' => $dates['end_at'],
                'end_at' => $dates['start_at']
            ]);

        $response->assertStatus(422);
    }

    /** @test Фильтрация по car_model_id */
    public function test_filter_by_car_model(): void
    {
        $director = User::factory()->create(['position_id' => 1]);
        $dates = $this->futureDateRange();

        $response = $this->actingAs($director, 'sanctum')
            ->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/available-cars', array_merge($dates, [
                'car_model_id' => 3
            ]));

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 2);
    }
}
