<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller;

use App\Models\RecursoVeiculoQuilometragem;
use App\Shared\Http\Controllers\Controller;

class DestroyQuilometragemVeiculoRecursoController extends Controller
{
  public function index(RecursoVeiculoQuilometragem $quilometragem)
  {
    try {
      $quilometragem->delete();

      return to_route('contratos.contratada.recurso.veiculo.create', ['contrato' => $quilometragem->contrato_id, 'veiculo' => $quilometragem->recurso_veiculo_id])->with('message', ['type' => 'success', 'content' => 'Km excluido com sucesso!']);
    } catch (\Exception $e) {
      return to_route('contratos.contratada.recurso.veiculo.create', ['contrato' => $quilometragem->contrato_id, 'veiculo' => $quilometragem->recurso_veiculo_id])->with('message', ['type' => 'error', 'content' => $e->getMessage()]);
    }
  }
}
