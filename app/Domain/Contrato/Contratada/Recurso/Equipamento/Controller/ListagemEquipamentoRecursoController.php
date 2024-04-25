<?php

namespace app\Domain\Contrato\Contratada\Recurso\Equipamento\Controller;

use App\Domain\Contrato\Contratada\Recurso\Equipamento\Services\EquipamentoRecursoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListagemEquipamentoRecursoController extends Controller
{
  public function __construct(private readonly EquipamentoRecursoService $equimentoRecursoService)
  {
  }

  public function index(Contrato $contrato, Request $request)
  {
    $searchParams = $request->all('searchColumn', 'searchValue');

    $response = $this->equimentoRecursoService->ListagemEquipamentos($contrato, $searchParams);

    return Inertia::render('Contrato/Contratada/Recurso/Equipamento/Index', [
      'contrato' => $contrato,
      ...$response
    ]);
  }
}
