<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller;

use App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Services\EmpreendimentoService;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services\LoteObraService;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\UpdateRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services\OcorrenciaService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\Servicos;
use App\Models\Uf;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UpdateController extends Controller
{
    public function __construct(private readonly OcorrenciaService $ocorrenciaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, UpdateRequest $request): RedirectResponse
    {
        $post = [
            ...$request->validated(),
            'id_servico' => $servico->id,
            'dias_restantes' => $request->prazo
        ];

        if ($request->form === 1) {
            $post['nome_id'] = $request['tipo'] . '.' . $request->num_por_servico . '.' . $request->rodovia['rodovia'] . '-' . Uf::find($request->rodovia['uf_id'])->uf;
            $post['id_rodovia'] = $request->rodovia['id'];
            $post['id_uf'] = $request->rodovia['uf_id'];
            $post['id_lote'] = $request->lote['id'];
        }

        $response = $this->ocorrenciaService->update($post);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'ocorrencia' => $request->id])->with('message', $response['request']);
    }
}
