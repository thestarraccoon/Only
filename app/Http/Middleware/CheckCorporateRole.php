<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\RoleConfig;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class CheckCorporateRole
{
    public function handle(Request $request, Closure $next): mixed
    {
        $corporateId = $request->header('X-Corporate-ID');

        if (!$corporateId) {
            throw new AuthorizationException(__('auth.access_denied'));
        }

        $user = $request->user();
        if (!$user) {
            throw new AuthenticationException(__('auth.unauthorized'));
        }

        $corporateRole = RoleConfig::fromCorporateId($corporateId);

        $userRole = RoleConfig::fromPositionId($user->position_id);

        if ($corporateRole !== $userRole) {
            throw new AuthorizationException(__('auth.access_denied'));
        }

        return $next($request);
    }
}
