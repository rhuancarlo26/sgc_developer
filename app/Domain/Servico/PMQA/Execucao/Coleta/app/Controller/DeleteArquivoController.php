<?php

namespace App\Domain\Servico\PMQA\Execucao\Coleta\app\Controller;

use App\Domain\Servico\PMQA\Execucao\Coleta\app\Services\ColetaService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaCampanhaPontoColetaArquivo;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteArquivoController extends Controller
{
  public function __construct(private readonly ColetaService $coletaService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaCampanha $campanha, ServicoPmqaCampanhaPontoColetaArquivo $arquivo): RedirectResponse
  {
    $response = $this->coletaService->deleteArquivo($arquivo);

    return to_route('contratos.contratada.servicos.pmqa.execucao.coleta.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'campanha' => $campanha->id, 'ponto' => $arquivo->id])->with('message', $response['request']);
  }
}