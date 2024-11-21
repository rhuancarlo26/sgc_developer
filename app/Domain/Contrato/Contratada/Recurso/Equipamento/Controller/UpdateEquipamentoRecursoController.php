<?php

namespace App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller;

use App\Domain\Contrato\Contratada\Recurso\Equipamento\Requests\UpdateEquipamentoRecursoRequest;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Services\EquipamentoRecursoService;
use App\Shared\Http\Controllers\Controller;

class UpdateEquipamentoRecursoController extends Controller
{
    public function __construct(private readonly EquipamentoRecursoService $equipamentoRecursoService)
    {
    }

    public function index(UpdateEquipamentoRecursoRequest $request)
    {
        $response = $this->equipamentoRecursoService->updateEquipamento($request->all());

        return to_route('contratos.contratada.recurso.equipamento.create', [
            'contrato' => $request->id_contrato,
            'equipamento' => $request->id
        ])->with('message', $response['request']);
    }
}
