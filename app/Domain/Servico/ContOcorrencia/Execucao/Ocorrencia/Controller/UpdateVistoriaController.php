<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller;

use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\UpdateVistoriaRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services\OcorrenciaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateVistoriaController extends Controller
{
    public function __construct(private readonly OcorrenciaService $ocorrenciaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, UpdateVistoriaRequest $request): RedirectResponse
    {
        $response = $this->ocorrenciaService->UpdateVistoria(post: $request->validated());

        return to_route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create_vistoria', ['contrato' => $contrato->id, 'servico' => $servico->id, 'ocorrencia' => $request['ocorrencia']['id']])->with('message', $response['request']);
    }
}
