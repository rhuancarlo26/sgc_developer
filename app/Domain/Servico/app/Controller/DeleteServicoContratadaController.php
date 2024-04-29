<?php

namespace App\Domain\Servico\app\Controller;

use App\Domain\Servico\app\Services\ServicoService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DeleteServicoContratadaController extends Controller
{
  public function __construct(private readonly ServicoService $servicoService)
  {
  }

  public function index(Servicos $servico)
  {
    try {
      $this->servicoService->delete($servico);

      return to_route('contratos.contratada.servicos.index', ['contrato' => $servico->contrato_id])->with('message', ['type' => 'success', 'content' => 'Serviço deletado!']);
    } catch (\Exception $e) {
      return to_route('contratos.contratada.servicos.index', ['contrato' => $servico->contrato_id])->with('message', ['type' => 'error', 'content' => $e->getMessage()]);
    }
  }
}
