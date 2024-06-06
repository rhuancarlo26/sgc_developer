<?php

namespace app\Domain\Contrato\Contratada\Recurso\Equipamento\Controller;

use App\Domain\Contrato\Contratada\Recurso\Equipamento\Services\EquipamentoRecursoService;
use App\Models\Contrato;
use App\Models\RecursoEquipamento;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;

class CreateEquipamentoRecursoController extends Controller
{
  public function __construct(private readonly EquipamentoRecursoService $equimentoRecursoService)
  {
  }

  public function index(Contrato $contrato, RecursoEquipamento $equipamento)
  {
    return Inertia::render('Contrato/Contratada/Recurso/Equipamento/Form', [
      'contrato' => $contrato,
      'equipamento' => $equipamento->load(['documentos'])
    ]);
  }
}
