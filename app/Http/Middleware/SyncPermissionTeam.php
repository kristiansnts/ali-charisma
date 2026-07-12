<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncPermissionTeam
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Filament::getTenant();

        if (! $tenant && Filament::hasTenancy() && $request->route()?->hasParameter('tenant')) {
            $panel = Filament::getCurrentPanel();
            $tenant = $panel?->getTenant($request->route()->parameter('tenant'));
        }

        if ($tenant) {
            setPermissionsTeamId($tenant);
        }

        return $next($request);
    }
}
