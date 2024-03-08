<?php

namespace App\Domain\Contrato\Contratada\Controller;

use App\Domain\Contrato\GestaoContrato\Requests\StoreContratoRequest;
use App\Models\Contrato;
use App\Models\ContratoIntroducao;
use App\Models\ContratoTipo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class StoreIntroducaoContratadaController extends Controller
{
  public function index(Request $request)
  {
    if ($introducao = ContratoIntroducao::create([
      ...$request->form,
      'contrato_id' => $request->contrato_id,
    ])) {
      return response()->json(['type' => 'success', 'introducao' => $introducao]);
    }

    return response()->json(data: ['type' => 'error'], status: 500);
  }
}
