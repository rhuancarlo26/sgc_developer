<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Controller;

use App\Domain\Servico\ContOcorrencia\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->resultadoService->index($servico->id, $searchParams);

        return Inertia::render('Servico/ContOcorr/Resultado/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo', 'cont_ocorr_parecer_configuracao']),
            ...$response
        ]);
    }
}
