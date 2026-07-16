<?php

namespace App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers;

use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Requests\UpdateConfigModuloRequest;
use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Services\ConfiguracoesModulosService;
use App\Models\SgcModulo;
use App\Shared\Http\Controllers\Controller;

class UpdateConfigModuloController extends Controller
{
    public function __construct(private readonly ConfiguracoesModulosService $service) {}

    public function update($contrato, $produto, SgcModulo $modulo, UpdateConfigModuloRequest $request)
    {
        $dataManagementRequest = $this->service->update($modulo, $request->validated());

        return to_route('sgc.contratada.produtos.modulos.configuracoes.formulario', [
            $contrato,
            $produto,
            $modulo->id,
        ])->with('message', $dataManagementRequest);
    }
}
