<?php

namespace App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers;

use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Requests\StoreConfigModuloRequest;
use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Services\ConfiguracoesModulosService;
use App\Shared\Http\Controllers\Controller;

class StoreConfigModuloController extends Controller
{
    public function __construct(private readonly ConfiguracoesModulosService $service) {}

    public function store($contrato, $produto, StoreConfigModuloRequest $request)
    {
        $dataManagement = $this->service->store($request->validated());

        return to_route('sgc.contratada.produtos.modulos.configuracoes.formulario', [
            $contrato,
            $produto,
            $dataManagement['model']->id,
        ])->with('message', $dataManagement['request']);
    }
}
