<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\app\Services;

use App\Models\SgcPmqa;
use App\Models\SgcPmqaParametroLista;
use App\Models\SgcPmqaPonto;
use App\Models\SgcvwEmpreendimentos;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PmqaService
{

    public function getCampanhas(int $contrato): Collection
    {
        $pmqas = SgcPmqa::where('id_contrato', $contrato)->get();

        $resumo = $this->getResumoVinculacoes($pmqas->pluck('id')->all());

        return $pmqas->map(fn($pmqa) => $this->mapearCampanha($pmqa, $resumo));
    }

    public function criarCampanha(int $contrato, string $subproduto): SgcPmqa
    {
        return SgcPmqa::create([
            'id_contrato'      => $contrato,
            'status_aprovacao' => 'Em analise',
            'subproduto'       => $subproduto,
        ]);
    }

    public function getEmpreendimentos(int $contrato): array
    {
        return SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->pluck('cod_emp')
            ->toArray();
    }

    private function mapearCampanha(SgcPmqa $pmqa, array $resumo): array
    {
        return [
            'id'             => $pmqa->id,
            'id_campanha'    => $pmqa->id,
            'id_contrato'    => $pmqa->id_contrato,
            'chave'          => $pmqa->chave ?? null,
            'tipo'           => $pmqa->tipo ?? 'PMQA',
            'empreendimento' => $pmqa->cod_emp ?? 'N/A',
            'subproduto'     => $pmqa->tipo ?? 'PMQA',
            'data_inicial'   => $pmqa->created_at?->format('d/m/Y') ?? 'N/A',
            'data_final'     => 'N/A',
            'status'         => $pmqa->status_aprovacao ?? 'rascunho',

            'vinculacoesResumo' => $resumo[$pmqa->id] ?? [
                'total_listas'           => 0,
                'total_pontos'           => 0,
                'total_pontos_vinculados' => 0,
            ],

            'tema'          => $pmqa->tema,
            'cod_emp'       => $pmqa->cod_emp,
            'especificacao' => $pmqa->especificacao,
            'introducao'    => $pmqa->introducao,
            'justificativa' => $pmqa->justificativa,
            'objetivos'     => $pmqa->objetivos,
            'metodologia'   => $pmqa->metodologia,
            'publico_alvo'  => $pmqa->publico_alvo,

            'status_aprovacao' => $pmqa->status_aprovacao,
            'created_at'       => $pmqa->created_at?->toISOString(),
            'updated_at'       => $pmqa->updated_at?->toISOString(),
            'deleted_at'       => $pmqa->deleted_at?->toISOString(),
        ];
    }

    private function getResumoVinculacoes(array $pmqaIds): array
    {
        if (empty($pmqaIds)) return [];

        $listas = SgcPmqaParametroLista::query()
            ->whereIn('pmqa_id', $pmqaIds)
            ->selectRaw('pmqa_id, COUNT(*) as total_listas')
            ->groupBy('pmqa_id')
            ->pluck('total_listas', 'pmqa_id')
            ->toArray();

        $pontos = SgcPmqaPonto::query()
            ->whereIn('pmqa_id', $pmqaIds)
            ->selectRaw('pmqa_id, COUNT(*) as total_pontos')
            ->groupBy('pmqa_id')
            ->pluck('total_pontos', 'pmqa_id')
            ->toArray();

        $pontosVinculados = DB::table('sgc_pmqa_config_ponto_lista')
            ->whereIn('pmqa_id', $pmqaIds)
            ->selectRaw('pmqa_id, COUNT(DISTINCT ponto_id) as total_pontos_vinculados')
            ->groupBy('pmqa_id')
            ->pluck('total_pontos_vinculados', 'pmqa_id')
            ->toArray();

        $map = [];
        foreach ($pmqaIds as $id) {
            $map[$id] = [
                'total_listas'            => $listas[$id] ?? 0,
                'total_pontos'            => $pontos[$id] ?? 0,
                'total_pontos_vinculados' => $pontosVinculados[$id] ?? 0,
            ];
        }

        return $map;
    }
}
