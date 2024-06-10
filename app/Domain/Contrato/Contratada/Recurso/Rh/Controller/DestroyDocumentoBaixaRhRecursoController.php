<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Models\Contrato;
use App\Models\RecursoRhDocumentoBaixa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DestroyDocumentoBaixaRhRecursoController extends Controller
{
  public function __construct(private readonly RhRecursoService $rhRecursoService)
  {
  }

  public function index(Contrato $contrato, RecursoRhDocumentoBaixa $documento_baixa)
  {
    try {
      Storage::delete($documento_baixa['caminho']);

      $this->rhRecursoService->delete($documento_baixa);

      return to_route('contratos.contratada.recurso.rh.create', ['contrato' => $contrato->id, 'rh' => $documento_baixa->recurso_rh_id])->with('message', ['type' => 'success', 'content' => 'Documento baixa excluido com sucesso!']);
    } catch (\Exception $e) {
      return to_route('contratos.contratada.recurso.rh.create', ['contrato' => $contrato->id, 'rh' => $documento_baixa->recurso_rh_id])->with('message', ['type' => 'error', 'content' => $e->getMessage()]);
    }
  }
}