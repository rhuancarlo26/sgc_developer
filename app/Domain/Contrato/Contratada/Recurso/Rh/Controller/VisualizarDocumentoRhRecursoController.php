<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Models\RecursoRhDocumento;
use App\Shared\Http\Controllers\Controller;

class VisualizarDocumentoRhRecursoController extends Controller
{
    public function __construct(private readonly RhRecursoService $rhRecursoService)
    {
    }

    public function index(RecursoRhDocumento $documento)
    {
        return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . $documento->arquivo);
    }
}
