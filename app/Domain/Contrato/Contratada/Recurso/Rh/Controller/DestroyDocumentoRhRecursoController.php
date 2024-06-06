<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Models\Contrato;
use App\Models\RecursoRhDocumento;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DestroyDocumentoRhRecursoController extends Controller
{
  public function __construct(private readonly RhRecursoService $rhRecursoService)
  {
  }

  public function index(Contrato $contrato, RecursoRhDocumento $documento)
  {
    try {
      Storage::delete($documento['caminho']);

      $this->rhRecursoService->delete($documento);

      return to_route('contratos.contratada.recurso.rh.create', ['contrato' => $contrato->id, 'rh' => $documento->recurso_rh_id])->with('message', ['type' => 'success', 'content' => 'Documento excluido com sucesso!']);
    } catch (\Exception $e) {
      return to_route('contratos.contratada.recurso.rh.create', ['contrato' => $contrato->id, 'rh' => $documento->recurso_rh_id])->with('message', ['type' => 'error', 'content' => $e->getMessage()]);
    }
  }
}
