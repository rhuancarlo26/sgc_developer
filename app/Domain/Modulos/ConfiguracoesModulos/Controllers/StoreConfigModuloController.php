<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Controllers;

use App\Domain\Modulos\ConfiguracoesModulos\Requests\StoreConfigModuloRequest;
use App\Domain\Modulos\ConfiguracoesModulos\Services\ConfiguracoesModulosService;
use App\Shared\Http\Controllers\Controller;

class StoreConfigModuloController extends Controller
{
    public function __construct(private readonly ConfiguracoesModulosService $service) {}

    public function store(StoreConfigModuloRequest $request)
    {
        $flashRequest = $this->service->store($request->validated());
        return to_route('modulos.config-modulos.index')->with('message', $flashRequest);
    }
}
