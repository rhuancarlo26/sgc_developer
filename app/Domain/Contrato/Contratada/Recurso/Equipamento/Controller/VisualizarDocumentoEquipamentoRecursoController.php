<?php

namespace App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller;

use App\Domain\Contrato\Contratada\Recurso\Equipamento\Services\EquipamentoRecursoService;
use App\Models\RecursoEquipamentoDocumento;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Utils\ArquivoUtils;
use Illuminate\Http\Request;

class VisualizarDocumentoEquipamentoRecursoController extends Controller
{
    public function __construct(private readonly EquipamentoRecursoService $equimentoRecursoService)
    {
    }

    public function index(RecursoEquipamentoDocumento $documento)
    {
        return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . $documento->arquivo);
    }
}
