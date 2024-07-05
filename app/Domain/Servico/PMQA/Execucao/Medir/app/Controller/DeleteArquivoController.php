<?php

namespace App\Domain\Servico\PMQA\Execucao\Medir\app\Controller;

use App\Domain\Servico\PMQA\Execucao\Medir\app\Services\MedicaoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaCampanhaPontoColetaArquivo;
use App\Models\ServicoPmqaCampanhaPontoMedicaoArquivo;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteArquivoController extends Controller
{
  public function __construct(private readonly MedicaoService $medicaoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaCampanha $campanha, ServicoPmqaCampanhaPontoMedicaoArquivo $arquivo): RedirectResponse
  {
    $arquivo->load(['medicao']);

    $response = $this->medicaoService->deleteArquivo($arquivo);

    return to_route('contratos.contratada.servicos.pmqa.execucao.coleta.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'campanha' => $campanha->id, 'ponto' => $arquivo->medicao['campanha_ponto_id']])->with('message', $response['request']);
  }
}