<?php

namespace App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers;

use App\Models\SgcModulo;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Traits\ModulosHandler;
use App\Shared\Traits\ProdutosReservados;
use Inertia\Inertia;
use Inertia\Response;

class CreateConfigModuloController extends Controller
{
    use ModulosHandler, ProdutosReservados;

    public function index($contrato, $produto, SgcModulo $modulo): Response
    {
        $produtosComModulo = SgcModulo::query()
            ->whereNotNull('produto_slug')
            ->select('produto_slug', 'produto_titulo')
            ->distinct()
            ->get();

        $slugsComModulo = $produtosComModulo->pluck('produto_slug')->all();

        $produtosLegadosSemModulo = collect($this->produtosLegadosLinkaveis())
            ->reject(fn($p) => in_array($p['produto_slug'], $slugsComModulo, true));

        $produtosDisponiveis = $produtosComModulo
            ->concat($produtosLegadosSemModulo)
            ->sortBy('produto_titulo')
            ->values();

        return Inertia::render('Sgc/Contratada/Produtos/Modulos/ConfigModulos/Form', [
            'modulo' => $modulo,
            'tipos' => $this->buscarParams(),
            'contrato' => $contrato,
            'produto' => $produto,
            'produtosDisponiveis' => $produtosDisponiveis,
        ]);
    }
}
