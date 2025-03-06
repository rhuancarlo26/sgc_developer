<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Models\Contrato;
use App\Models\RecursoRh;
use App\Models\RecursoRhDocumento;
use App\Shared\Http\Controllers\Controller;

class DestroyFotoPerfilController extends Controller
{
  public function __construct(private readonly RhRecursoService $rhRecursoService) {}

  public function index(RecursoRh $rh)
  {
    $response = $this->rhRecursoService->destroyFotoPerfil($rh);

    return to_route(route: 'contratos.contratada.recurso.rh.create', parameters: ['contrato' => $rh->id_contrato, 'rh' => $rh->id])
      ->with('message', $response['request']);
  }
}
