<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Services\TrechoContratoService;
use App\Models\ContratoTipo;
use App\Models\contratoTrecho;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DestroyContratoTrechoController extends Controller
{
  public function __construct(private readonly TrechoContratoService $trechoContratoService)
  {
  }

  public function destroyTrecho(ContratoTipo $tipo, contratoTrecho $trecho): RedirectResponse
  {
    try {
      $this->trechoContratoService->delete($trecho);

      $response = ['type' => 'success', 'content' => 'Trecho do contrato deletado com sucesso'];
    } catch (\Throwable $th) {
      $response = ['type' => 'error', 'content' => 'Falha ao excluir o trecho do contrato'];
    }

    return to_route('contratos.gestao.create', ['tipo' => $tipo, 'contrato' => $trecho->contrato_id])->with('message', $response);
  }
}