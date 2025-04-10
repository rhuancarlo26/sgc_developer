<?php

namespace App\Domain\Servico\PMQA\Relatorio\app\Controller;

use App\Domain\Servico\PMQA\Relatorio\app\Requests\StoreRequest;
use App\Domain\Servico\PMQA\Relatorio\app\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\ServicoPmqaRelatorio;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
  public function __construct(private readonly RelatorioService $relatorioService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaRelatorio $relatorio): RedirectResponse
  {
    $response = $this->relatorioService->destroy(relatorio: $relatorio);

    return to_route('contratos.contratada.servicos.pmqa.relatorio.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
