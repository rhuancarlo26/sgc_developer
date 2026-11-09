<?php

namespace App\Domain\Sgc\Contratada\Produtos\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureFaunaProduct
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->route('produto') !== 'fauna') {
            abort_unless($request->isMethod('GET'), 404);
            return to_route('sgc.contratada.produtos.index', [$request->route('contrato'), 'fauna']);
        }

        if (str_starts_with($request->route()->getName(), 'sgc.contratada.produtos.modulos.configuracoes.')) {
            $user = $request->user();
            abort_unless((int) $user->perfis_id === 3 || $user->hasRole('Super Admin'), 403);
        }

        return $next($request);
    }
}
