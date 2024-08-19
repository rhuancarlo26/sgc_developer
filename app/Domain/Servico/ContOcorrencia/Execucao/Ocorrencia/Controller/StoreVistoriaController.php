<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller;

use App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Services\EmpreendimentoService;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services\LoteObraService;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreRegistroRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreVistoriaArquivoRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreVistoriaRequest;
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

class StoreVistoriaController extends Controller
{
    public function __construct(private readonly OcorrenciaService $ocorrenciaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreVistoriaRequest $request): RedirectResponse
    {
        $post = $request->validated();

        $post['vistoria']['id_ocorrencia'] = $post['ocorrencia']['id'];
        $post['vistoria']['num_por_ocorrencia'] = (ServicoConOcorrSupervisaoExecOcorrenciaVistoria::where('id_ocorrencia', $post['ocorrencia']['id'])->orderby('num_por_ocorrencia', 'desc')->first()->num_por_ocorrencia ?? 0) + 1;
        $post['vistoria']['nome_id'] = $post['ocorrencia']['nome_id'] . ".(vis." . $post['vistoria']['num_por_ocorrencia'] . ")";

        $response = $this->ocorrenciaService->storeVistoria($post);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create_vistoria', ['contrato' => $contrato->id, 'servico' => $servico->id, 'ocorrencia' => $post['ocorrencia']['id']])->with('message', $response['request']);
    }
}
