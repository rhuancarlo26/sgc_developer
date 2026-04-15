<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Requests\StoreImportadorRequest;
use App\Domain\Modulos\Importador\Services\StoreService;
use App\Shared\Http\Controllers\Controller;

class StoreImportadorController extends Controller
{
    public function __construct(
        private StoreService $service
    ) {
        // 
    }

    public function store(StoreImportadorRequest $request)
    {
        $dataManagement = $this->service->store($request->validated());
        return to_route('modulos.config-modulos.formulario', [$dataManagement['model']->id])
            ->with('message', $dataManagement['request']);
    }
}
