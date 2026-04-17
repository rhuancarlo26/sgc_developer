<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Requests\AnalisarImportadorRequest;
use App\Domain\Modulos\Importador\Requests\AprovReprovImportadorRequest;
use App\Domain\Modulos\Importador\Services\StatusImportadorService;
use App\Models\ModuloImportador;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StatusImportadorController extends Controller
{
    public function __construct(
        private StatusImportadorService $service
    ) {
        // 
    }

    public function enviarAnalise(ModuloImportador $importador, AnalisarImportadorRequest $request): RedirectResponse
    {
        $this->service->enviarAnalise($importador, $request->validated());

        $dataManagement = [
            'type'    => 'success',
            'content' => 'Importação enviada para análise!'
        ];

        return to_route('modulos.importador.index')->with('message', $dataManagement);
    }

    public function aprovReprov(ModuloImportador $importador, int $status, AprovReprovImportadorRequest $request): RedirectResponse
    {
        $this->service->aprovReprov($importador, $status, $request->validated());

        $dataManagement = [
            'type'    => 'success',
            'content' => 'Importação enviada para análise!'
        ];

        return to_route('modulos.importador.index')->with('message', $dataManagement);
    }
}
