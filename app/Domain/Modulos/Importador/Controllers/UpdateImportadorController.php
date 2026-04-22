<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Requests\UpdateImportadorRequest;
use App\Domain\Modulos\Importador\Services\UpdateService;
use App\Models\ModuloImportador;
use App\Shared\Http\Controllers\Controller;

class UpdateImportadorController extends Controller
{
    public function __construct(
        private UpdateService $service
    ) {
        // 
    }

    public function update(ModuloImportador $importador, UpdateImportadorRequest $request)
    {
        $this->service->update($importador, $request->validated());
        $dataManagement = [
            'type'    => 'success',
            'content' => 'Dados Atualizados com sucesso!'
        ];

        return to_route('modulos.importador.index')->with('message', $dataManagement);
    }
}
