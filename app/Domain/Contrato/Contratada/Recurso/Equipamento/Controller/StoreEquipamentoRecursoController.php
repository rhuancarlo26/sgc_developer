<?php

namespace App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller;

use App\Domain\Contrato\Contratada\Recurso\Equipamento\Requests\StoreEquipamentoRecursoRequest;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Services\EquipamentoRecursoService;
use App\Shared\Http\Controllers\Controller;

class StoreEquipamentoRecursoController extends Controller
{
    public function __construct(private readonly EquipamentoRecursoService $equimentoRecursoService)
    {
    }

    public function index(StoreEquipamentoRecursoRequest $request)
    {
        $response = $this->equimentoRecursoService->salvarEquipamento($request->all());

        return to_route('contratos.contratada.recurso.equipamento.create', [
            'contrato'    => $request->id_contrato,
            'equipamento' => $response['equipamento']['id']
        ])->with('message', $response['request']);
    }
}
