<?php

namespace App\Domain\Auth\Middlewares;

use Closure;
use Illuminate\Http\Request;

class RoutePermissionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $route = $request->route()->getName() ?? $request->route()->uri();

        if (!$user->hasRole('Super Admin') && !$user->can($route)) {

            $message = "Usuário sem autorização";

            return !$request->inertia() && $request->expectsJson()
                ? response(['message' => $message], 403)
                : back()->with('message', ['type' => 'error', 'content' => $message]);
        }

        return $next($request);
    }
}
