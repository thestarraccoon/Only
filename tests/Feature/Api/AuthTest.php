<?php

namespace Tests\Feature\Api;

use App\Enums\RoleConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /** @test Регистрация Директора */
    public function test_register_director(): void
    {
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
        $response = $this->withHeaders(['X-Corporate-ID' => 'corp-scp-001'])
            ->postJson('/api/auth/register', [
                'name' => 'Иван Спец',
                'email' => 'spec@test.com',
                'password' => '123qweasd',
                'password_confirmation' => '123qweasd'
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.user.roles', ['specialist'])
            ->assertJsonPath('data.user.position.id', 3); // Спец

        $this->assertDatabaseHas('users', [
            'email' => 'spec@test.com',
            'position_id' => 3
        ]);
    }

    /** @test position_id игнорируется */
    public function test_register_ignores_position_id(): void
    {
        $response = $this->withHeaders(['X-Corporate-ID' => 'corp-scp-001'])
            ->postJson('/api/auth/register', [
                'name' => 'Иван',
                'email' => 'test_ignore_pos@test.com',
                'password' => '123qweasd',
                'password_confirmation' => '123qweasd',
                'position_id' => 1
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.user.position.id', 3); // specialist = 3!
    }

    /** @test Валидация email unique */
    public function test_register_email_already_exists(): void
    {
        User::create([
            'name' => 'Существующий',
            'email' => 'testauthemailexists@test.com',
            'password' => Hash::make('123qweasd'),
            'position_id' => 1
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Иван',
            'email' => 'testauthemailexists@test.com',
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
            'email' => 'testruslocale@ru.com',
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
                RoleConfig::SPECIALIST => 'corp-scp-001'
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

    public function test_user_can_login_and_get_token(): void
    {
        $password = '123qweasd';

        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $this->assertEquals(0, $user->tokens()->count());

        $response = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals(1, $user->tokens()->count());
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(422);
    }

    public function test_login_fails_when_user_not_found(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'unknown@example.com',
            'password' => 'some-password',
        ]);

        $response
            ->assertStatus(422);
    }

    public function test_login_with_revoke_other_tokens_deletes_previous_tokens(): void
    {
        $password = '123qweasd';

        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $oldToken1 = $user->createToken('old_1')->plainTextToken;
        $oldToken2 = $user->createToken('old_2')->plainTextToken;

        $this->assertEquals(2, $user->tokens()->count());

        $response = $this->postJson('/api/auth/login', [
            'email'               => $user->email,
            'password'            => $password,
            'revoke_other_tokens' => true,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('message', __('auth.success'));

        $user->refresh();

        $this->assertEquals(1, $user->tokens()->count());

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id'   => $user->id,
            'tokenable_type' => User::class,
        ]);
    }
}
