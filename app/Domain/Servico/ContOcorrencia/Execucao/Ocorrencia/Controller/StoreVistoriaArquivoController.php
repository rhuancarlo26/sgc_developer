<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller;

use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreVistoriaArquivoRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services\OcorrenciaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class StoreVistoriaArquivoController extends Controller
{
    public function __construct(private readonly OcorrenciaService $ocorrenciaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreVistoriaArquivoRequest $request): \Illuminate\Http\RedirectResponse
    {
        $response = $this->ocorrenciaService->storeVistoriaArquivo($request->validated());

        return to_route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create_vistoria', ['contrato' => $contrato->id, 'servico' => $servico->id, 'ocorrencia' => $request->id_ocorrencia])->with('message', $response['request']);
    }
}
