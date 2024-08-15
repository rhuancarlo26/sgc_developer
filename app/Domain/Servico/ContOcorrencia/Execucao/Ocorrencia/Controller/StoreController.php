<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller;

use App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Services\EmpreendimentoService;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services\LoteObraService;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreRequest;
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

class StoreController extends Controller
{
    public function __construct(private readonly OcorrenciaService $ocorrenciaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreRequest $request): RedirectResponse
    {
        $lastOcorrencia = ServicoConOcorrOcorrenciSupervisaoExecOcorrencia::where('id_servico', $servico->id)->where('tipo', $request->tipo)->orderby('num_por_servico', 'DESC')->first() ?? 0;
        $nome = $request['tipo'] . '.' . str_pad(($lastOcorrencia->num_por_servico ?? 0) + 1, 2, '0', STR_PAD_LEFT) . '.' . $request->rodovia['rodovia'] . '-' . Uf::find($request->rodovia['estados_id'])->uf;

        $post = [
            ...$request->validated(),
            'nome_id' => $nome,
            'num_por_servico' => str_pad(($lastOcorrencia->num_por_servico ?? 0) + 1, 2, '0', STR_PAD_LEFT),
            'id_servico' => $servico->id,
            'id_rodovia' => $request->rodovia['id'],
            'id_uf' => $request->rodovia['estados_id'],
            'id_lote' => $request->lote['id'],
            'dias_restantes' => $request->prazo,
            'status' => 'Em aberto',
            'envio_empresa' => 'Não'
        ];

        $response = $this->ocorrenciaService->store($post);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'ocorrencia' => $response['model']['id']])->with('message', $response['request']);
    }
}
