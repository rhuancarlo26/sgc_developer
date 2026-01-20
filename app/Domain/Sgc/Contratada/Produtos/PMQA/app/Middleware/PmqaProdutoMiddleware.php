<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\app\Middleware;

use Closure;
use Illuminate\Http\Request;

class PmqaProdutoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // por enquanto não valida nada
        // só libera o fluxo
        return $next($request);
    }
}
