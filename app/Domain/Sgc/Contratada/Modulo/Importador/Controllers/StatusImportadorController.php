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

    private function redirectImportador(ModuloImportador $importador, array $dataManagement): RedirectResponse
    {
        $importador->refresh();

        if ($importador->servico_id) {
            return to_route('modulos.importador.formulario', [
                'importador' => $importador->id,
                'contrato_id' => $importador->contrato_id,
                'modulo_id' => $importador->modulo_id,
                'servico_id' => $importador->servico_id,
                'origem_servico' => true,
            ])->with('message', $dataManagement);
        }

        return to_route('modulos.importador.index')->with('message', $dataManagement);
    }

    public function enviarAnalise(ModuloImportador $importador, AnalisarImportadorRequest $request): RedirectResponse
    {
        $this->service->enviarAnalise($importador, $request->validated());

        $dataManagement = [
            'type'    => 'success',
            'content' => 'Importação enviada para análise!'
        ];

        return $this->redirectImportador($importador, $dataManagement);
    }

    public function aprovReprov(ModuloImportador $importador, int $status, AprovReprovImportadorRequest $request): RedirectResponse
    {
        $this->service->aprovReprov($importador, $status, $request->validated());

        $dataManagement = [
            'type'    => 'success',
            'content' => 'Importação atualizada com sucesso!'
        ];

        return $this->redirectImportador($importador, $dataManagement);
    }
}
