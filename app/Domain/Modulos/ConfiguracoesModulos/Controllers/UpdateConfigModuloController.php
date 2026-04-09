<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Controllers;

use App\Domain\Modulos\ConfiguracoesModulos\Requests\UpdateConfigModuloRequest;
use App\Domain\Modulos\ConfiguracoesModulos\Services\ConfiguracoesModulosService;
use App\Models\Modulo;
use App\Shared\Http\Controllers\Controller;

class UpdateConfigModuloController extends Controller
{
    public function __construct(private readonly ConfiguracoesModulosService $service) {}

    public function update(Modulo $modulo, UpdateConfigModuloRequest $request)
    {
        $flashRequest = $this->service->update($modulo, $request->validated());
        return to_route('modulos.config-modulos.index')->with('message', $flashRequest);
    }
}
