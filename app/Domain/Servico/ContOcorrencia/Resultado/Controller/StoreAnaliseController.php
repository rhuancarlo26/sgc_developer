<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Controller;

use App\Domain\Servico\ContOcorrencia\Resultado\Requests\StoreAnaliseRequest;
use App\Domain\Servico\ContOcorrencia\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrSupervisaoResultado;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class StoreAnaliseController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrSupervisaoResultado $resultado, StoreAnaliseRequest $request)
    {
        if ($request->validated('id')) {
            $response = $this->resultadoService->updateAnalise($request->validated());
        } else {
            $response = $this->resultadoService->storeAnalise($request->validated());
        }

        return to_route('contratos.contratada.servicos.cont_ocorrencia.resultado.resultado', ['contrato' => $contrato->id, 'servico' => $servico->id, 'resultado' => $resultado->id])->with('message', $response['request']);
    }
}
