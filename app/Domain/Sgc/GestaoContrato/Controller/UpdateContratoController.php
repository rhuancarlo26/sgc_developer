<?php

namespace App\Domain\Sgc\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Requests\UpdateContratoRequest;
use App\Domain\Contrato\GestaoContrato\Services\ListagemContratoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UpdateContratoController extends Controller
{
  public function __construct(private readonly ListagemContratoService $listagemContrato)
  {
  }

  public function update(Contrato $contrato, UpdateContratoRequest $request)
  {
    $post = [
      ...$request->all(),
      'user_id' => Auth::user()->id,
    ];

    $response = $this->listagemContrato->update($post);

    return to_route('contratos.gestao.create', ['tipo' => $contrato->tipo_id, 'contrato' => $contrato->id])->with('message', $response['request']);
  }
}