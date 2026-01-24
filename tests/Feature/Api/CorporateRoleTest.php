<?php
// tests/Feature/Api/CorporateRoleTest.php

namespace Tests\Feature\Api;

use App\Enums\RoleConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorporateRoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test RoleConfig маппинг */
    public function test_role_config_mapping(): void
    {
        $this->assertEquals('director', RoleConfig::fromCorporateId('corp-dir-001')->value);
        $this->assertEquals('manager', RoleConfig::fromCorporateId('corp-mgr-123')->value);
        $this->assertEquals('specialist', RoleConfig::fromCorporateId('unknown')->value);
    }

    /** @test Все Corporate ID префиксы */
    public function test_all_corporate_id_prefixes(): void
    {
        $tests = [
            'corp-dir-ABC123' => RoleConfig::DIRECTOR,
            'corp-mgr-XYZ789' => RoleConfig::MANAGER,
            'corp-spec-DEF456' => RoleConfig::SPECIALIST,
            'invalid-prefix' => RoleConfig::SPECIALIST
        ];

        foreach ($tests as $corporateId => $expectedRole) {
            $this->assertEquals($expectedRole, RoleConfig::fromCorporateId($corporateId));
        }
    }
}
