<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\RoleConfig;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class CheckCorporateRole
{
    public function handle(Request $request, Closure $next, string ...$allowedRoles): mixed
    {
        $corporateId = $request->header('X-Corporate-ID');

        if (!$corporateId) {
            throw new AuthorizationException(__('auth.access_denied'));
        }

        return $next($request);
    }
}
