<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Services\ListagemContratoService;
use App\Models\Contrato;
use App\Models\ContratoTipo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Inertia\Inertia;

class GetServicoController extends Controller
{
  public function __construct(private readonly ListagemContratoService $listagemContrato) {}

  public function index(Contrato $contrato)
  {
    return response()->json($this->listagemContrato->getServicos($contrato));
  }
}
