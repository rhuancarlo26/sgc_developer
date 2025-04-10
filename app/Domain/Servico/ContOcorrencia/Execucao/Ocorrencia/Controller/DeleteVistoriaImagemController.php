<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller;

use App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Services\EmpreendimentoService;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services\LoteObraService;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services\OcorrenciaService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaVistoria;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaVistoriaImg;
use App\Models\Servicos;
use App\Models\Uf;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeleteVistoriaImagemController extends Controller
{
    public function __construct(private readonly OcorrenciaService $ocorrenciaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrOcorrenciSupervisaoExecOcorrencia $ocorrencia, ServicoConOcorrSupervisaoExecOcorrenciaVistoria $vistoria, ServicoConOcorrSupervisaoExecOcorrenciaVistoriaImg $imagem): RedirectResponse
    {
        $response = $this->ocorrenciaService->destroyVistoriaImagem($imagem);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create_vistoria', ['contrato' => $contrato->id, 'servico' => $servico->id, 'ocorrencia' => $vistoria->id_ocorrencia])->with('message', $response['request']);
    }
}
