<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller;

use App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Services\EmpreendimentoService;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services\LoteObraService;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreRegistroRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreVistoriaArquivoRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services\OcorrenciaService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaVistoria;
use App\Models\Servicos;
use App\Models\Uf;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UpdateVistoriaController extends Controller
{
    public function __construct(private readonly OcorrenciaService $ocorrenciaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreVistoriaArquivoRequest $request): RedirectResponse
    {
        $response = $this->ocorrenciaService->UpdateVistoria(post: $request->validated());

        return to_route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create_vistoria', ['contrato' => $contrato->id, 'servico' => $servico->id, 'ocorrencia' => $request['ocorrencia']['id']])->with('message', $response['request']);
    }
}
