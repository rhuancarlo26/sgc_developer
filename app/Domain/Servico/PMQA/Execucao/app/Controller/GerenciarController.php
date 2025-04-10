<?php

namespace App\Domain\Servico\PMQA\Execucao\app\Controller;

use App\Domain\Servico\PMQA\Execucao\app\Services\CampanhaPontoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GerenciarController extends Controller
{
    public function __construct(private readonly CampanhaPontoService $campanhaPontoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPmqaCampanha $campanha, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->campanhaPontoService->index($campanha, $searchParams);

        return Inertia::render('Servico/PMQA/Execucao/Gerenciar', [
            'contrato' => $contrato,
            'servico'  => $servico->load(['tipo', 'pmqa_config_lista_parecer']),
            'campanha' => $campanha,
            ...$response
        ]);
    }
}
