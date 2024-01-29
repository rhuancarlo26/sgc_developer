<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Services\TrechoContratoService;
use App\Models\Contrato;
use App\Models\contratoTrecho;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DestroyContratoTrechoController extends Controller
{
  public function __construct(private readonly TrechoContratoService $trechoContratoService)
  {
  }

  /**
   * @param contratoTrecho $trecho
   * @return RedirectResponse
   */
  public function destroyTrecho(contratoTrecho $trecho): RedirectResponse
  {
    $this->trechoContratoService->delete($trecho);

    return to_route('contratos.gestao.create', $trecho->contrato_id)->with('message', [
      'type' => 'info',
      'content' => "Contrato deletado com sucesso"
    ]);
  }
}