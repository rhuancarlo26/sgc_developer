<?php

namespace App\Domain\Servico\ContOcorrencia\Relatorio\Controller;

use App\Domain\Servico\ContOcorrencia\Relatorio\Services\RelatorioService;
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

        $response = $this->relatorioService->index($servico, $searchParams);

        return Inertia::render('Servico/ContOcorr/Relatorio/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load([
                'tipo',
                'cont_ocorr_parecer_configuracao',
                'rhs',
                'equipamentos',
                'veiculos.codigo',
                'licencas_condicionantes.licenca.tipo_rel',
                'licencas_condicionantes.condicionante',
            ]),
            ...$response
        ]);
    }
}
