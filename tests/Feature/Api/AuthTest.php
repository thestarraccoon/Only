<?php

namespace Tests\Feature\Api;

use App\Enums\RoleConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test Регистрация Директора */
    public function test_register_director(): void
    {
        $this->seed();

        $response = $this->withHeaders(['X-Corporate-ID' => 'corp-dir-001'])
            ->postJson('/api/auth/register', [
                'name' => 'Иван Директор',
                'email' => 'director@test.com',
                'password' => '123qweasd',
                'password_confirmation' => '123qweasd'
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.user.roles', ['director'])
            ->assertJsonPath('data.user.position.id', 1); // Директор

        $this->assertDatabaseHas('users', [
            'email' => 'director@test.com',
            'position_id' => 1
        ]);
    }

    /** @test Регистрация Специалиста (по умолчанию) */
    public function test_register_specialist_default(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Иван',
            'email' => 'specialist@test.com',
            'password' => '123qweasd',
            'password_confirmation' => '123qweasd'
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.user.roles', ['specialist'])
            ->assertJsonPath('data.user.position.id', 3);
    }

    /** @test position_id игнорируется */
    public function test_register_ignores_position_id(): void
    {
        $response = $this->withHeaders(['X-Corporate-ID' => 'corp-spec-001'])
            ->postJson('/api/auth/register', [
                'name' => 'Иван',
                'email' => 'test@test.com',
                'password' => '123qweasd',
                'password_confirmation' => '123qweasd',
                'position_id' => 1 // Игнорируется!
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.user.position.id', 3); // specialist = 3!
    }

    /** @test Валидация email unique */
    public function test_register_email_already_exists(): void
    {
        User::create([
            'name' => 'Существующий',
            'email' => 'testauth@test.com',
            'password' => Hash::make('123qweasd'),
            'position_id' => 1
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Иван',
            'email' => 'testauth@test.com',
            'password' => '123qweasd',
            'password_confirmation' => '123qweasd'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    /** @test Русская локализация */
    public function test_register_russian_locale(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'ru',
            'X-Corporate-ID' => 'corp-dir-001'
        ])->postJson('/api/auth/register', [
            'name' => 'Иван',
            'email' => 'test@ru.com',
            'password' => '123qweasd',
            'password_confirmation' => '123qweasd'
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', __('auth.register_success'));
    }

    /** @test Все роли работают */
    public function test_all_roles_registration(): void
    {
        foreach (RoleConfig::cases() as $role) {
            $corporateId = match($role) {
                RoleConfig::DIRECTOR => 'corp-dir-001',
                RoleConfig::MANAGER => 'corp-mgr-001',
                RoleConfig::SPECIALIST => 'corp-spec-001'
            };

            $response = $this->withHeaders(['X-Corporate-ID' => $corporateId])
                ->postJson('/api/auth/register', [
                    'name' => $role->value,
                    'email' => $role->value . 'all_roles' . '@test.com',
                    'password' => '123qweasd',
                    'password_confirmation' => '123qweasd'
                ]);

            $response->assertStatus(201)
                ->assertJsonPath('data.user.roles.0', $role->value);
        }
    }
}
