<?php

namespace App\Domain\Fiscal\app\services;

use App\Models\Servicos;
use App\Models\ServicoTema;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class FiscalService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Servicos::class;

    public function listagemServicos($contrato, $searchParams, array $filtros = []): array
    {
        $baseQuery = Servicos::query()
            ->where('id_contrato', $contrato->id)
            ->whereNull('deleted_at')
            ->whereIn('status_aprovacao', [2, 3, 4]);

        $temasIds = (clone $baseQuery)
            ->whereNotNull('tema_servico')
            ->distinct()
            ->pluck('tema_servico')
            ->filter()
            ->values();

        $temasFiltro = ServicoTema::query()
            ->whereIn('id', $temasIds)
            ->orderBy('nome_tema')
            ->get(['id', 'nome_tema']);

        $statusFiltro = (clone $baseQuery)
            ->whereNotNull('status_aprovacao')
            ->distinct()
            ->pluck('status_aprovacao')
            ->map(fn($status) => [
                'id' => (int) $status,
                'nome' => $this->nomeStatusAprovacao((int) $status),
            ])
            ->filter(fn($status) => $status['nome'] !== '-')
            ->sortBy('id')
            ->values();

        $query = $this->search(...$searchParams)
            ->with([
                'tipo',
                'tema',
                'rhs',
                'veiculos',
                'veiculos.codigo',
                'equipamentos',
                'condicionantes',
                'condicionantes.licenca',
                'parecer',
                'moduloImportado:id,nome,pmqa,contrato_id',
                'moduloImportado.contrato:id,numero_contrato',
            ])
            ->where('id_contrato', $contrato->id)
            ->whereNull('deleted_at')
            ->whereIn('status_aprovacao', [2, 3, 4])
            ->when($filtros['filtro_tema_id'] ?? null, function ($query, $temaId) {
                $query->where('tema_servico', $temaId);
            })
            ->when($filtros['filtro_servico'] ?? null, function ($query, $servico) {
                $query->where(function ($query) use ($servico) {
                    $query->where('servico', 'like', "%{$servico}%")
                        ->orWhereHas('tipo', function ($query) use ($servico) {
                            $query->where('nome', 'like', "%{$servico}%");
                        })
                        ->orWhereHas('moduloImportado', function ($query) use ($servico) {
                            $query->where('nome', 'like', "%{$servico}%");
                        });
                });
            })
            ->when($filtros['filtro_especificacao'] ?? null, function ($query, $especificacao) {
                $query->where('especificacao', 'like', "%{$especificacao}%");
            })
            ->when($filtros['filtro_licenca'] ?? null, function ($query, $licenca) {
                $query->whereHas('condicionantes.licenca', function ($query) use ($licenca) {
                    $query->where('numero_licenca', 'like', "%{$licenca}%");
                });
            })
            ->when($filtros['filtro_status_aprovacao'] ?? null, function ($query, $status) {
                $query->where('status_aprovacao', $status);
            });

        return [
            'servicos' => $query->paginate()->appends([
                ...$searchParams,
                ...$filtros,
            ]),
            'temasFiltro' => $temasFiltro,
            'statusFiltro' => $statusFiltro,
            'filtros' => $filtros,
        ];
    }

    private function nomeStatusAprovacao(int $status): string
    {
        return match ($status) {
            1 => 'Em confecção',
            2 => 'Em análise',
            3 => 'Aprovado',
            4 => 'Pendente',
            default => '-',
        };
    }

    public function listagemConfiguracao($contrato, $searchParams, $id_servico): array
    {
        $query = $this->search(...$searchParams)
            ->with([
                'tema',
                'tipo',
                'parecer',
                'parecerPmqa',
                'parecerSupressaoVegetacao',
                'parecerAfugentamento',
                'parecerOcorrencia',
                'parecer_passagem_fauna',
                'pontos',
                'parametros',
                'parametros.pontos',
                'supervisao_lotes.uf',
                'supervisao_lotes.rodovia',
                'licencas_condicionantes.licenca.tipo_rel',
                'passagem_fauna_passagens',
                'passagem_fauna_abios.licenca.tipo_rel',
                'parecer_atropelamento',
                'atropelamento_abios.licenca.tipo_rel'
            ])
            ->where('id_contrato', $contrato->id)
            ->where('servico', $id_servico)
            ->where('deleted_at', null)
            ->whereIn('status_aprovacao', [2, 3, 4]);

        return ['servicos' => $query->paginate()->appends($searchParams)];
    }
}
