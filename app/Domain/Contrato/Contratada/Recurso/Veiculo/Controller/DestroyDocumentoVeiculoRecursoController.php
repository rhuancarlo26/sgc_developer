<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller;

use App\Domain\Contrato\Contratada\Recurso\Veiculo\Services\VeiculoRecursoService;
use App\Models\Contrato;
use App\Models\RecursoVeiculoDocumento;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DestroyDocumentoVeiculoRecursoController extends Controller
{
    public function __construct(private readonly VeiculoRecursoService $veiculoRecursoService)
    {
    }

    public function index(Contrato $contrato, RecursoVeiculoDocumento $documento)
    {
        try {
            Storage::delete($documento['arquivo']);

            $this->veiculoRecursoService->delete($documento);

            return to_route('contratos.contratada.recurso.veiculo.create', ['contrato' => $contrato->id, 'veiculo' => $documento->cod_veiculo])->with('message', ['type' => 'success', 'content' => 'Documento excluido com sucesso!']);
        } catch (\Exception $e) {
            return to_route('contratos.contratada.recurso.veiculo.create', ['contrato' => $contrato->id, 'veiculo' => $documento->cod_veiculo])->with('message', ['type' => 'error', 'content' => $e->getMessage()]);
        }
    }
}
