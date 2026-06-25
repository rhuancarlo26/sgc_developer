<?php

namespace App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers;

use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Services\ConfiguracoesModulosService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConfiguracoesModulosController extends Controller
{
    public function __construct(
        private ConfiguracoesModulosService $service
    ) {
        // 
    }

    public function index(Request $request, $contrato, $produto): Response
    {
        $searchParams = $request->all('columns', 'value');

        $data = $this->service->buscarModulos($searchParams);

        return Inertia::render('Sgc/Contratada/Produtos/Modulos/ConfigModulos/Index', [
            ...$data,
            'contrato' => $contrato,
            'produto' => $produto,
        ]);
    }
}
