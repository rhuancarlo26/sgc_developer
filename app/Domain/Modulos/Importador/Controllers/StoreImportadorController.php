<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Requests\StoreImportadorRequest;
use App\Domain\Modulos\Importador\Services\StoreService;
use App\Models\Servicos;
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

            $servico = $importador->servico_id
                ? Servicos::query()->find($importador->servico_id)
                : null;

            $contexto = [
                'contrato_id' => $importador->contrato_id,
                'modulo_id' => $importador->modulo_id,
                'servico_id' => $importador->servico_id,
                'tema_id' => $servico?->tema_servico ?? $request->input('tema_id'),
                'origem_servico' => $importador->servico_id ? true : null,
            ];

            $dataManagement = [
                'type' => 'success',
                'content' => 'Importação iniciada com sucesso!'
            ];

            if ($request->boolean('continuar_formulario')) {
                return to_route('modulos.importador.formulario', $contexto + [
                    'importador' => $importador->id,
                ])->with('message', $dataManagement);
            }

            return to_route('modulos.importador.index', $contexto)
                ->with('message', $dataManagement);
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
