<?php

namespace app\Domain\Contrato\Contratada\Historico\Controller;

use App\Domain\Contrato\Contratada\Historico\Services\HistoricoService;
use App\Models\ContratoEmpreendimentoTrecho;
use App\Models\ContratoHistorico;
use App\Shared\Http\Controllers\Controller;

class DestroyHistoricoContratadaController extends Controller
{
  public function __construct(private readonly HistoricoService $historicoService)
  {
  }

  public function index(ContratoHistorico $historico)
  {
    $contrato_id = $historico->contrato_id;

    try {
      $this->historicoService->delete($historico);

      return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $contrato_id])->with('message', ['type' => 'success', 'content' => 'Registro atualizado!']);
    } catch (\Exception $e) {
      return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $contrato_id])->with('message', ['type' => 'error', 'content' => $e->getMessage()]);
    }
  }
}