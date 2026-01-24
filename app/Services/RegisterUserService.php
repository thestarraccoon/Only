<?php

namespace App\Services;

use App\Enums\RoleConfig;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUserService
{
    public function register(array $userData, ?string $corporateId): User
    {
        $role = RoleConfig::fromCorporateId($corporateId ?? '');

        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'position_id' => $role->positionId(),
        ]);

        $user->assignRole($role->value);

        return $user;
    }
}
