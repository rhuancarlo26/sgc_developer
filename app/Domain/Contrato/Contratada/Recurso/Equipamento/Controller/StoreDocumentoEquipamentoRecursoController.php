<?php

namespace App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller;

use App\Domain\Contrato\Contratada\Recurso\Equipamento\Services\EquipamentoRecursoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreDocumentoEquipamentoRecursoController extends Controller
{
    public function __construct(private readonly EquipamentoRecursoService $equimentoRecursoService)
    {
    }

    public function index(Request $request)
    {
        $response = $this->equimentoRecursoService->salvarDocumentoEquipamento($request->all());

        return to_route('contratos.contratada.recurso.equipamento.create', [
            'contrato' => $request->contrato_id,
            'equipamento' => $request->cod_equipamento
        ])->with('message', $response);
    }
}
