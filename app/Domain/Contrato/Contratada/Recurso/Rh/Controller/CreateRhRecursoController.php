<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Services\VeiculoRecursoService;
use App\Models\Contrato;
use App\Models\RecursoRh;
use App\Models\RecursoVeiculo;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;

class CreateRhRecursoController extends Controller
{
  public function __construct(private readonly RhRecursoService $rhRecursoService)
  {
  }

  public function index(Contrato $contrato, RecursoRh $rh)
  {
    return Inertia::render('Contrato/Contratada/Recurso/Rh/Form', [
      'contrato' => $contrato,
      'rh' => $rh->load(['documentos', 'documentosBaixa']),
    ]);
  }
}
