<?php

namespace App\Domain\Servico\ContOcorrencia\Relatorio\Controller;

use App\Domain\Servico\ContOcorrencia\Relatorio\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrSupervisaoResultado;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetVariaveisController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrSupervisaoResultado $resultado): JsonResponse
    {
        $response = $this->relatorioService->getVariaveis($resultado);

        return response()->json(data: $response, status: 200);
    }
}
