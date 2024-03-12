<?php

namespace App\Domain\Contrato\Contratada\Introducao\Controller;

use App\Domain\Contrato\Contratada\Introducao\Services\IntroducaoService;
use App\Domain\Contrato\GestaoContrato\Requests\StoreContratoRequest;
use App\Models\Contrato;
use App\Models\ContratoIntroducao;
use App\Models\ContratoTipo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class UpdateIntroducaoContratadaController extends Controller
{
  public function __construct(private readonly IntroducaoService $introducaoService)
  {
  }

  public function index(ContratoIntroducao $introducao, Request $request)
  {
    $response = $this->introducaoService->update($request->all());

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $request->contrato_id])->with('message', $response['request']);
  }
}