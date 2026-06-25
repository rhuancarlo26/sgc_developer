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

        $importador->refresh();

        $dataManagement = [
            'type'    => 'success',
            'content' => 'Dados atualizados com sucesso!'
        ];

        $contexto = [
            'importador' => $importador->id,
            'contrato_id' => $importador->contrato_id,
            'modulo_id' => $importador->modulo_id,
            'servico_id' => $importador->servico_id,
            'origem_servico' => $importador->servico_id ? true : null,
        ];

        if ($importador->servico_id) {
            return to_route('modulos.importador.formulario', $contexto)
                ->with('message', $dataManagement);
        }

        return to_route('modulos.importador.index')->with('message', $dataManagement);
    }
}
