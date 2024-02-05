<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Services\ListagemContratoService;
use App\Models\Contrato;
use App\Models\ContratoTipo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DestroyContratoController extends Controller
{
  public function __construct(private readonly ListagemContratoService $listagemContrato)
  {
  }

  /**
   * @param Contrato $role
   * @return RedirectResponse
   */
  public function destroy(Contrato $contrato): RedirectResponse
  {
    $this->listagemContrato->delete($contrato);

    return to_route('contratos.gestao.listagem', $contrato->tipo_id)->with('message', [
      'type' => 'info',
      'content' => "Contrato deletado com sucesso"
    ]);
  }
}
