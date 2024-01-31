<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateContratoController extends Controller
{
  public function update(Contrato $contrato, Request $request)
  {
    if ($contrato->update([
      ...$request->all(),
      'user_id' => Auth::user()->id,
    ])) {
      return to_route('contratos.gestao.create', ['tipo' => $contrato->tipo_id, 'contrato' => $contrato->id])->with('message', [
        'type' => 'success',
        'content' => "Contrato atualizado com sucesso"
      ]);
    }
  }
}