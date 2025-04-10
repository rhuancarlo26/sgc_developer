<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller;

use App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Services\EmpreendimentoService;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services\LoteObraService;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services\OcorrenciaService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervicaoExecOcorrenciaRegistro;
use App\Models\Servicos;
use App\Models\Uf;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeleteRegistroController extends Controller
{
  public function __construct(private readonly OcorrenciaService $ocorrenciaService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrSupervicaoExecOcorrenciaRegistro $registro): RedirectResponse
  {
    $response = $this->ocorrenciaService->destroyRegistro($registro);

    return to_route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'ocorrencia' => $registro->id_ocorrencia])->with('message', $response['request']);
  }
}
