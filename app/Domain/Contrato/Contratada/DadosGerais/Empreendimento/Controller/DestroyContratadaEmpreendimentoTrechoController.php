<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Empreendimento\Controller;

use App\Domain\Contrato\Contratada\DadosGerais\Empreendimento\Services\EmpreendimentoTrechoService;
use App\Models\ContratoEmpreendimentoTrecho;
use App\Shared\Http\Controllers\Controller;

class DestroyContratadaEmpreendimentoTrechoController extends Controller
{
  public function __construct(private readonly EmpreendimentoTrechoService $empreendimentoTrechoService)
  {
  }

  public function index(ContratoEmpreendimentoTrecho $trecho)
  {
    $contrato_id = $trecho->contrato_id;

    try {
      $this->empreendimentoTrechoService->delete($trecho);

      return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $contrato_id])->with('message', ['type' => 'success', 'content' => 'Registro atualizado!']);
    } catch (\Exception $e) {
      return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $contrato_id])->with('message', ['type' => 'error', 'content' => $e->getMessage()]);
    }
  }
}
