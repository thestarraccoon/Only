<?php

namespace App\Enums;

enum RoleConfig: string
{
    case DIRECTOR = 'director';
    case MANAGER = 'manager';
    case SPECIALIST = 'specialist';

    public static function fromCorporateId(string $corporateId): self
    {
        return match (true) {
            str_starts_with($corporateId, 'corp-dir-') => self::DIRECTOR,
            str_starts_with($corporateId, 'corp-mgr-') => self::MANAGER,
            default => self::SPECIALIST
        };
    }

    public function positionId(): int
    {
        return match($this) {
            self::DIRECTOR => 1,
            self::MANAGER => 2,
            self::SPECIALIST => 3
        };
    }

    public static function fromPositionId(int $positionId): self
    {
        return match($positionId) {
            1 => self::DIRECTOR,
            2 => self::MANAGER,
            3 => self::SPECIALIST,
            default => self::SPECIALIST
        };
    }

}
