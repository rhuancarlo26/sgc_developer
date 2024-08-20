<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Controller;

use App\Domain\Servico\ContOcorrencia\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrSupervisaoResultado;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ResultadoController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrSupervisaoResultado $resultado): Response
    {
        $response = $this->resultadoService->resultado($resultado);

        return Inertia::render('Servico/ContOcorr/Resultado/Resultado', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            ...$response
        ]);
    }
}
