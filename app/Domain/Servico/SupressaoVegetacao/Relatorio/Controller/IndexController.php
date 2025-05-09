<?php

namespace App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller;


use App\Domain\Servico\SupressaoVegetacao\Relatorio\Services\RelatorioService;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{

    public function __construct(
        private readonly ResultadoService $resultadoService,
        private readonly RelatorioService $relatorioService,
    )
    {
    }

    public function __invoke(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');
        return Inertia::render(component: 'Servico/SupressaoVegetacao/Relatorio/Index', props: [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            'data' => $this->relatorioService->index(servico: $servico, searchParams: $searchParams),
            'resultados' => $this->resultadoService->getByServico(servico: $servico),
        ]);
    }

}
