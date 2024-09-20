<?php

namespace App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller;

use App\Domain\Contrato\Contratada\Recurso\Equipamento\Services\EquipamentoRecursoService;
use App\Models\Contrato;
use App\Models\RecursoEquipamento;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DestroyEquipamentoRecursoController extends Controller
{
    public function __construct(private readonly EquipamentoRecursoService $equimentoRecursoService)
    {
    }

    public function index(RecursoEquipamento $equipamento)
    {
        try {
            $response = $this->equimentoRecursoService->destroyEquipamento($equipamento);

            $this->equimentoRecursoService->delete($equipamento);

            return to_route('contratos.contratada.recurso.equipamento.index', ['contrato' => $equipamento->id_contrato])->with('message', $response);
        } catch (\Exception $e) {
            return to_route('contratos.contratada.recurso.equipamento.index', ['contrato' => $equipamento->id_contrato])->with('message', $response);
        }
    }
}
