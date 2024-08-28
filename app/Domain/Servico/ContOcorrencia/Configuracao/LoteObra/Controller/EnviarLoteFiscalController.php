<?php

namespace App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller;

use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Requests\StoreRequest;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services\LoteObraService;
use App\Models\Contrato;
use App\Models\ServicoContOcorrSupervisaoConfigLote;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class EnviarLoteFiscalController extends Controller
{
    public function __construct(private readonly LoteObraService $loteObraService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreRequest $request): RedirectResponse
    {
        $post = [
            'fk_servico' => $servico->id,
            'fk_status' => 3
        ];

        $response = $this->loteObraService->enviarLoteFiscal($post);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
    }
}
