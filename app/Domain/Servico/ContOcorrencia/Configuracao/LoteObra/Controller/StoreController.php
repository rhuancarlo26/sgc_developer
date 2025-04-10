<?php

namespace App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller;

use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Requests\StoreRequest;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services\LoteObraService;
use App\Models\Contrato;
use App\Models\Rodovia;
use App\Models\ServicoContOcorrSupervisaoConfigLote;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    public function __construct(private readonly LoteObraService $loteObraService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreRequest $request): RedirectResponse
    {
        $lote = ServicoContOcorrSupervisaoConfigLote::where('id_servico', $servico->id)->latest('id')->first();
        $numPorServico = $lote ? $lote->num_por_servico + 1 : 1;

        $post = [
            ...$request->validated(),
            'num_por_servico' => $numPorServico,
            'nome_id' => 'L.0' . $numPorServico,
            'id_rodovia' => $request->rodovia['id'],
            'id_uf' => $request->rodovia['uf']['id']
        ];

        $response = $this->loteObraService->store($post);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'lote' => $response['model']['id'] ?? null])->with('message', $response['request']);
    }
}
