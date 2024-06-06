<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Services\ListagemContratoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DestroyContratoController extends Controller
{
  public function __construct(private readonly ListagemContratoService $listagemContrato)
  {
  }

  public function destroy(Contrato $contrato): RedirectResponse
  {
    try {
      $this->listagemContrato->delete($contrato);

      $response = ['type' => 'success', 'content' => 'Contrato deletado com sucesso'];
    } catch (\Throwable $th) {
      $response = ['type' => 'error', 'content' => 'Falha ao excluir o contrato'];
    }

    return to_route('contratos.gestao.listagem', $contrato->tipo_id)->with('message', $response);
  }
}