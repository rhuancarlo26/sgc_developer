<?php

namespace App\Utils;

use Illuminate\Support\Facades\Route;

class RouteUtils
{
    /**
     * Return routes array
     *
     * @return array<int,\Illuminate\Routing\Route>
     */
    public static function getAllRoutes(): array
    {
        return array_values(iterator_to_array(Route::getRoutes()));
    }

    /**
     * Return route(s) array by middleware(s) alias
     *
     * @param string|array $middlewares
     * @return array<int,\Illuminate\Routing\Route>
     */
    public static function getRoutesByMiddleware(string|array $middlewares): array
    {
        $middlewares = is_array($middlewares) ? $middlewares : explode(',', $middlewares);

        return array_values(array_filter(self::getAllRoutes(), function (\Illuminate\Routing\Route $route) use ($middlewares) {
            return (bool)array_filter($route->middleware(), fn($middleware) => in_array($middleware, $middlewares));
        }));
    }
}
