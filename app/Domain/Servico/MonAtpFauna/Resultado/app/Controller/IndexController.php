<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services\CampanhasService;
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
        private readonly ResultadoService $resultadoService,
        private readonly CampanhasService $campanhasService,
    )
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        return Inertia::render('Servico/MonAtpFauna/Resultado/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo', 'pmqa_config_lista_parecer']),
            'campanhas' => $this->campanhasService->getCampanhas(servicoId: $servico->id, searchParams: ['', ''], paginate: false),
            'data' => $this->resultadoService->index($servico, $searchParams),
        ]);
    }
}
