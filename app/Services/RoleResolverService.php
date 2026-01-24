<?php

namespace App\Services;

use App\Enums\RoleConfig;

class RoleResolverService
{
    public function resolveRoleByCorporateId(?string $corporateId): string
    {
        return RoleConfig::fromCorporateId($corporateId ?? '')->value;
    }

    public function getPositionIdByRole(string $roleName): int
    {
        return RoleConfig::from($roleName)->positionId();
    }
}
