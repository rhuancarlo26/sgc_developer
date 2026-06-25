<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Requests\StoreImportadorRequest;
use App\Domain\Modulos\Importador\Services\StoreService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreImportadorController extends Controller
{
    public function __construct(
        private StoreService $service
    ) {
        //
    }

    public function store(StoreImportadorRequest $request)
    {
        try {
            $importador = $this->service->store($request->validated());

            $dataManagement = [
                'type'    => 'success',
                'content' => 'Importação iniciada com sucesso!'
            ];

            if ($request->boolean('continuar_formulario')) {
                return to_route('modulos.importador.formulario', [
                    'importador' => $importador->id,
                    'contrato_id' => $importador->contrato_id,
                    'modulo_id' => $importador->modulo_id,
                    'servico_id' => $importador->servico_id,
                    'origem_servico' => $importador->servico_id ? true : null,
                ])->with('message', $dataManagement);
            }

            return to_route('modulos.importador.index')->with('message', $dataManagement);
        } catch (Throwable $e) {
            Log::error('Erro ao criar importação do módulo', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withErrors([
                    'arquivo' => 'Não foi possível iniciar a importação. Verifique se a planilha está no modelo correto.',
                ])
                ->with('message', [
                    'type' => 'error',
                    'content' => 'Erro ao iniciar importação. Verifique o arquivo enviado.',
                ]);
        }
    }
}
