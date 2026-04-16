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
        $this->service->store($request->validated());
        $dataManagement = [
            'type'    => 'success',
            'content' => 'Importação iniciada com sucesso!'
        ];

        return to_route('modulos.importador.index')->with('message', $dataManagement);
    }
}
