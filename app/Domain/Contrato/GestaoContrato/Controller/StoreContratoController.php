<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Requests\StoreContratoRequest;
use App\Models\Contrato;
use App\Models\ContratoTipo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class StoreContratoController extends Controller
{
  public function store(StoreContratoRequest $request)
  {
    if ($contrato = Contrato::create([
      ...$request->all(),
      'user_id' => Auth::user()->id,
    ])) {
      return to_route('contratos.gestao.create', ['tipo' => $contrato->tipo_id, 'contrato' => $contrato->id])->with('message', [
        'type' => 'success',
        'content' => "Contrato criado com sucesso"
      ]);
    }
  }
}