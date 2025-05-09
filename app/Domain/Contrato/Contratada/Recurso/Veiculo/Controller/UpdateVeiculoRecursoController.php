<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller;

use App\Domain\Contrato\Contratada\Recurso\Veiculo\Requests\UpdateVeiculoRecursoRequest;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Services\VeiculoRecursoService;
use App\Shared\Http\Controllers\Controller;

class UpdateVeiculoRecursoController extends Controller
{
    public function __construct(private readonly VeiculoRecursoService $veiculoRecursoService)
    {
    }

    public function index(UpdateVeiculoRecursoRequest $request)
    {
        $response = $this->veiculoRecursoService->updateVeiculo($request->all());

        return to_route('contratos.contratada.recurso.veiculo.create', [
            'contrato' => $request->id_contrato,
            'veiculo' => $request->id
        ])->with('message', $response['request']);
    }
}
