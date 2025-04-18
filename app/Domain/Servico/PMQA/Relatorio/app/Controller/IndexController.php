<?php

namespace App\Domain\Servico\PMQA\Relatorio\app\Controller;

use App\Domain\Servico\PMQA\Execucao\app\Services\CampanhaService;
use App\Domain\Servico\PMQA\Relatorio\app\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->relatorioService->index(servico: $servico, searchParams: $searchParams);

        return Inertia::render('Servico/PMQA/Relatorio/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load([
                'tipo',
                'pmqa_config_lista_parecer',
                'rhs',
                'equipamentos',
                'veiculos.codigo',
                'licencas_condicionantes.licenca',
                'licencas_condicionantes.condicionante',
                'pontos'
            ]),
            ...$response
        ]);
    }
}
