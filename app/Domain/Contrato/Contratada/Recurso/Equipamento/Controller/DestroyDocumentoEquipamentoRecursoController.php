<?php

namespace App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller;

use App\Domain\Contrato\Contratada\Recurso\Equipamento\Services\EquipamentoRecursoService;
use App\Models\Contrato;
use App\Models\RecursoEquipamentoDocumento;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Utils\ArquivoUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestroyDocumentoEquipamentoRecursoController extends Controller
{
    public function __construct(private readonly EquipamentoRecursoService $equimentoRecursoService)
    {
    }

    public function index(Contrato $contrato, RecursoEquipamentoDocumento $documento)
    {
        try {
            Storage::delete($documento['arquivo']);

            $this->equimentoRecursoService->delete($documento);

            return to_route('contratos.contratada.recurso.equipamento.create', ['contrato' => $contrato->id, 'equipamento' => $documento->cod_equipamento])->with('message', ['type' => 'success', 'content' => 'Documento excluido com sucesso!']);
        } catch (\Exception $e) {
            return to_route('contratos.contratada.recurso.equipamento.create', ['contrato' => $contrato->id, 'equipamento' => $documento->cod_equipamento])->with('message', ['type' => 'error', 'content' => $e->getMessage()]);
        }
    }
}
