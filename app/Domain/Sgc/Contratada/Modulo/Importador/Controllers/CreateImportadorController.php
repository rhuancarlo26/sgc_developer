<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Services\ImportadorService;
use App\Models\ModuloImportador;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Modulo;
use App\Models\Contrato;

class CreateImportadorController extends Controller
{
    public function __construct(
        private ImportadorService $service
    ) {
        //
    }

    public function create(ModuloImportador $importador, Request $request): Response
    {
        $contexto = $request->only([
            'contrato_id',
            'modulo_id',
            'servico_id',
            'tema_id',
            'origem_servico',
        ]);

        if ($importador->exists) {
            $importador->load(['fotos', 'anexos', 'modulo', 'contrato']);
            $importador->append('status_formatado');

            if (empty($contexto['contrato_id'])) {
                $contexto['contrato_id'] = $importador->contrato_id;
            }

            if (empty($contexto['modulo_id'])) {
                $contexto['modulo_id'] = $importador->modulo_id;
            }

            if (empty($contexto['servico_id'])) {
                $contexto['servico_id'] = $importador->servico_id;
            }

            if (!empty($importador->servico_id)) {
                $contexto['origem_servico'] = true;
            }
        } else {
            $importador->forceFill([
                'contrato_id' => $contexto['contrato_id'] ?? null,
                'modulo_id' => $contexto['modulo_id'] ?? null,
                'servico_id' => $contexto['servico_id'] ?? null,
            ]);

            if (!empty($contexto['modulo_id'])) {
                $importador->setRelation('modulo', Modulo::find($contexto['modulo_id']));
            }

            if (!empty($contexto['contrato_id'])) {
                $importador->setRelation('contrato', Contrato::find($contexto['contrato_id']));
            }
        }

        $campanhasUsadas = ModuloImportador::query()
            ->when($contexto['contrato_id'] ?? null, function ($query, $contratoId) {
                $query->where('contrato_id', $contratoId);
            })
            ->when($contexto['modulo_id'] ?? null, function ($query, $moduloId) {
                $query->where('modulo_id', $moduloId);
            })
            ->when($contexto['servico_id'] ?? null, function ($query, $servicoId) {
                $query->where('servico_id', $servicoId);
            })
            ->when($importador->exists, function ($query) use ($importador) {
                $query->where('id', '!=', $importador->id);
            })
            ->pluck('campanha')
            ->map(fn($campanha) => (int) $campanha)
            ->toArray();

        $campanhasDisponiveis = collect(range(1, 10))
            ->reject(fn($campanha) => in_array($campanha, $campanhasUsadas))
            ->values()
            ->all();

        return Inertia::render('Modulos/Importador/Form', [
            'moduloImportador' => $importador,
            'modulos' => $this->service->buscarModulos(),
            'contratos' => $this->service->buscarContratos(),
            'contextoImportador' => $contexto,
            'campanhasDisponiveis' => $campanhasDisponiveis,
        ]);
    }
}
