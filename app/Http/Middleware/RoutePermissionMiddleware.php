<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoutePermissionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $route = $request->route()->getName() ?? $request->route()->uri();

        if (!$user->hasRole('Super Admin') && !$user->can($route)) {
            return back()->with('message', ['type' => 'error', 'content' => 'Usuário sem autorização']);
        }

        return $next($request);
    }
}
