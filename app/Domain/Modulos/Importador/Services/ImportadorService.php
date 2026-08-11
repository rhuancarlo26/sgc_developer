<?php

namespace App\Domain\Modulos\Importador\Services;

use App\Models\Contrato;
use App\Models\Modulo;
use App\Models\ModuloImportador;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\ModulosHandler;
use App\Shared\Traits\Searchable;
use Illuminate\Database\Eloquent\Collection;

class ImportadorService extends BaseModelService
{
    use ModulosHandler, Searchable;

    protected string $modelClass = ModuloImportador::class;

    public function buscarImportadores(array $searchParams, array $contexto = [], array $filtros = []): array
    {
        $modulos = Modulo::query()
            ->with(['contrato:id,numero_contrato'])
            ->whereNull('deleted_at')
            ->orderBy('nome')
            ->get([
                'id',
                'nome',
                'pmqa',
                'contrato_id',
            ]);

        $with = [
            'modulo:id,nome,pmqa,contrato_id',
            'modulo.contrato:id,numero_contrato',
            'contrato:id,numero_contrato,contratada',
            'servico.tema:id,nome_tema',
            'licencas.tipo_rel:id,sigla',
        ];

        $baseContextoQuery = ModuloImportador::query()
            ->with($with)
            ->when($contexto['contrato_id'] ?? null, function ($query, $contratoId) {
                $query->where('contrato_id', $contratoId);
            })
            ->when($contexto['modulo_id'] ?? null, function ($query, $moduloId) {
                $query->where('modulo_id', $moduloId);
            })
            ->when($contexto['servico_id'] ?? null, function ($query, $servicoId) {
                $query->where('servico_id', $servicoId);
            });

        $query = $this->searchAllColumns(...$searchParams)
            ->with($with)
            ->when($contexto['contrato_id'] ?? null, function ($query, $contratoId) {
                $query->where('contrato_id', $contratoId);
            })
            ->when($contexto['modulo_id'] ?? null, function ($query, $moduloId) {
                $query->where('modulo_id', $moduloId);
            })
            ->when($contexto['servico_id'] ?? null, function ($query, $servicoId) {
                $query->where('servico_id', $servicoId);
            })
            ->when($filtros['filtro_modulo_id'] ?? null, function ($query, $moduloId) {
                $query->where('modulo_id', $moduloId);
            })
            ->when($filtros['filtro_tema_id'] ?? null, function ($query, $temaId) {
                $query->whereHas('servico', function ($query) use ($temaId) {
                    $query->where('tema_servico', $temaId);
                });
            })
            ->when($filtros['campanha'] ?? null, function ($query, $campanha) {
                $query->where('campanha', $campanha);
            })
            ->when($filtros['updated_at'] ?? null, function ($query, $updatedAt) {
                $query->whereDate('updated_at', $updatedAt);
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id');

        $importadores = $query
            ->paginate(10)
            ->appends([
                ...$searchParams,
                ...$contexto,
                ...$filtros,
            ]);

        $importadores->getCollection()->each(function ($item) {
            $item->append('status_formatado');
            $item->append('revisao');
        });

        $campanhasUsadas = (clone $baseContextoQuery)
            ->pluck('campanha')
            ->map(fn($campanha) => (int) $campanha)
            ->toArray();

        $campanhasDisponiveis = collect(range(1, 10))
            ->reject(fn($campanha) => in_array($campanha, $campanhasUsadas))
            ->values()
            ->all();

        $temasFiltro = (clone $baseContextoQuery)
            ->get()
            ->pluck('servico.tema')
            ->filter()
            ->unique('id')
            ->values();

        if (!empty($contexto['tema'])) {
            $temasFiltro = $temasFiltro
                ->push($contexto['tema'])
                ->filter()
                ->unique('id')
                ->values();
        }

        $temasFiltro = $temasFiltro
            ->map(function ($tema) {
                return [
                    'id' => $tema->id,
                    'nome_tema' => $tema->nome_tema,
                ];
            })
            ->values();

        $campanhasFiltro = (clone $baseContextoQuery)
            ->whereNotNull('campanha')
            ->distinct()
            ->orderBy('campanha')
            ->pluck('campanha')
            ->map(fn($campanha) => (int) $campanha)
            ->values();

        $modulosFiltro = (clone $baseContextoQuery)
            ->get()
            ->pluck('modulo')
            ->filter()
            ->unique('id')
            ->values();

        if (!empty($contexto['modulo'])) {
            $modulosFiltro = $modulosFiltro
                ->push($contexto['modulo'])
                ->filter()
                ->unique('id')
                ->values();
        }

        $modulosFiltro = $modulosFiltro
            ->map(function ($modulo) {
                return [
                    'id' => $modulo->id,
                    'nome' => $modulo->nome,
                    'pmqa' => (bool) $modulo->pmqa,
                    'contrato_id' => $modulo->contrato_id,
                    'contrato' => $modulo->contrato ? [
                        'id' => $modulo->contrato->id,
                        'numero_contrato' => $modulo->contrato->numero_contrato,
                    ] : null,
                ];
            })
            ->values();

        return [
            'modulos' => $modulos,
            'importadores' => $importadores,
            'contextoImportador' => $contexto,
            'campanhasDisponiveis' => $campanhasDisponiveis,
            'modulosFiltro' => $modulosFiltro,
            'temasFiltro' => $temasFiltro,
            'campanhasFiltro' => $campanhasFiltro,
            'filtros' => $filtros,
        ];
    }

    public function buscarModulos(): Collection
    {
        return Modulo::query()
            ->with(['contrato:id,numero_contrato'])
            ->whereNull('deleted_at')
            ->orderBy('nome')
            ->get([
                'id',
                'nome',
                'pmqa',
                'contrato_id',
                'campos',
            ]);
    }

    public function buscarContratos(): Collection
    {
        return Contrato::query()
            ->orderBy('numero_contrato')
            ->get([
                'id',
                'numero_contrato',
                'contratada',
            ]);
    }
}
