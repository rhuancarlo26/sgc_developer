<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller;

use App\Domain\Contrato\Contratada\Recurso\Veiculo\Services\VeiculoRecursoService;
use App\Models\RecursoEquipamentoDocumento;
use App\Models\RecursoVeiculoDocumento;
use App\Shared\Http\Controllers\Controller;

class VisualizarDocumentoVeiculoRecursoController extends Controller
{
    public function __construct(private readonly VeiculoRecursoService $veiculoRecursoService)
    {
    }

    public function index(RecursoVeiculoDocumento $documento)
    {
        return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . $documento->arquivo);
    }
}
