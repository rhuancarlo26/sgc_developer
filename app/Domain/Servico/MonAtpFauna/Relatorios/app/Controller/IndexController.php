<?php

namespace App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller;

use App\Domain\Servico\MonAtpFauna\Relatorios\app\Services\RelatoriosService;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Services\ResultadoService;
use App\Domain\Servico\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(
        private readonly RelatoriosService $relatoriosService,
        private readonly ResultadoService $resultadoService,
    ) {}

    public function __invoke(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        return Inertia::render('Servico/MonAtpFauna/Relatorios/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo', 'parecer_atropelamento']),
            'data' => $this->relatoriosService->index($servico, $searchParams),
            'resultados' => $this->resultadoService->index(servico: $servico, searchParams: ['', ''], paginate: false),
        ]);
    }
}