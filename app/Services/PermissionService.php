<?php

namespace App\Services;

use App\Utils\RouteUtils;
use Illuminate\Routing\Route;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    /**
     * Sync permissions based on routes with the RoutePermissionMiddleware
     *
     * @return void
     */
    public function updateRoutesPermissions(): void
    {
        $routes = RouteUtils::getRoutesByMiddleware('route-permission');

        foreach ($routes as $route) {
            Permission::firstOrCreate([
                'name' => $route->action['as'] ?? $route->uri()
            ]);
        }

        Permission::query()
            ->whereNotIn('name', array_map(fn(Route $route) => $route->action['as'] ?? $route->uri(), $routes))
            ->delete();
    }

    /**
     * Get Permissions ids by based on its names
     *
     * @param array $names
     * @return array
     */
    public function getPermissionsIdsByNames(array $names): array
    {
        return Permission::query()
            ->whereIn('name', $names)
            ->get(['id'])
            ->toArray();
    }
}
