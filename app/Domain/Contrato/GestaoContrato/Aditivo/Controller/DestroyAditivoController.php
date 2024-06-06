<?php

namespace App\Domain\Contrato\GestaoContrato\Aditivo\Controller;

use App\Domain\Contrato\GestaoContrato\Aditivo\Services\AditivoService;
use App\Models\ContratoAditivo;
use App\Models\ContratoTipo;
use App\Shared\Http\Controllers\Controller;

class DestroyAditivoController extends Controller
{
  public function __construct(private readonly AditivoService $aditivoService)
  {
  }

  public function index(ContratoTipo $tipo, ContratoAditivo $aditivo)
  {
    try {
      $this->aditivoService->delete($aditivo);

      $response = ['type' => 'success', 'content' => 'Aditivo deletado com sucesso'];
    } catch (\Throwable $th) {
      $response = ['type' => 'error', 'content' => $th->getMessage()];
    }

    return to_route('contratos.gestao.create', ['tipo' => $tipo->id, 'contrato' => $aditivo->contrato_id])->with('message', $response);
  }
}
