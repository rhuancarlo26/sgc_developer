<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Services\ImportadorService;
use App\Models\ModuloImportador;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Modulo;
use App\Models\Contrato;
use App\Models\Licenca;
use App\Models\Servicos;

class CreateImportadorController extends Controller
{
    public function __construct(
        private ImportadorService $service
    ) {
        //
    }

    public function create(ModuloImportador $importador, Request $request): Response|RedirectResponse
    {
        $contexto = [
            'contrato_id' => $request->input('contrato_id'),
            'modulo_id' => $request->input('modulo_id'),
            'servico_id' => $request->input('servico_id'),
            'tema_id' => $request->input('tema_id'),
            'origem_servico' => $request->boolean('origem_servico'),
            'origem_fiscal' => $request->boolean('origem_fiscal'),
            'modo_fiscal' => $request->boolean('modo_fiscal') || $request->boolean('origem_fiscal'),
        ];

        $contexto = array_filter($contexto, function ($valor) {
            return $valor !== null && $valor !== '';
        });

        if ($importador->exists) {
            $importador->load([
                'fotos',
                'anexos',
                'modulo.contrato:id,numero_contrato',
                'contrato',
                'licencas.tipo_rel:id,sigla',
            ]);
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

        if (!$this->servicoAprovado($contexto['servico_id'] ?? null)) {
            return back()->with('message', [
                'type' => 'warning',
                'content' => 'O importador só será habilitado após a aprovação do fiscal no cadastro do serviço.',
            ]);
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
            'licencas' => Licenca::query()
                ->with(['tipo_rel:id,sigla'])
                ->orderBy('numero_licenca')
                ->get()
                ->map(function ($licenca) {
                    $empreendimento = $licenca->empreendimento
                        ?? collect([
                            $licenca->inicio_subtrecho,
                            $licenca->fim_subtrecho,
                        ])
                        ->filter()
                        ->implode(' a ');

                    return [
                        'id' => $licenca->id,
                        'numero_licenca' => $licenca->numero_licenca,

                        'tipo' => $licenca->tipo,
                        'tipo_rel' => $licenca->tipo_rel ? [
                            'id' => $licenca->tipo_rel->id,
                            'sigla' => $licenca->tipo_rel->sigla,
                        ] : null,

                        'empreendimento' => $empreendimento ?: null,
                        'emissor' => $licenca->emissor ?? null,
                        'data_emissao' => $licenca->data_emissao,
                        'status' => $licenca->status,
                        'status_formatado' => $licenca->status_formatado ?? null,
                        'vencimento' => $licenca->vencimento,
                        'processo_dnit' => $licenca->processo_dnit,

                        'inicio_subtrecho' => $licenca->inicio_subtrecho,
                        'fim_subtrecho' => $licenca->fim_subtrecho,
                    ];
                })
                ->values(),
        ]);
    }

    private function servicoAprovado(?int $servicoId): bool
    {
        if (!$servicoId) {
            return true;
        }

        return Servicos::query()
            ->where('id', $servicoId)
            ->where('status_aprovacao', 3)
            ->exists();
    }
}
