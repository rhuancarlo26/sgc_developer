<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Introducao\Services\IntroducaoService;
use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Domain\Contrato\GestaoContrato\Requests\StoreContratoRequest;
use App\Models\Contrato;
use App\Models\ContratoIntroducao;
use App\Models\ContratoTipo;
use App\Models\RecursoRh;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class UpdateRhRecursoController extends Controller
{
  public function __construct(private readonly RhRecursoService $rhRecursoService)
  {
  }

  public function index(RecursoRh $rh, Request $request)
  {
    $response = $this->rhRecursoService->updateRh($request->all());

    return to_route('contratos.contratada.recurso.rh.create', ['contrato' => $request->contrato_id, 'rh' => $request->id])->with('message', $response['request']);
  }
}