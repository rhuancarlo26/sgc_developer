<?php

namespace App\Domain\Servico\SupervisaoAmbiental\Execucao\Registros\Service;

use App\Models\Servicos;
use App\Models\SupervisaoAmbientalModel;
use Illuminate\Support\Facades\DB;

class RegistrosService
{

    public function graficos_monitora_supervisao(Servicos $servico): array
    {
        $allRegistros = SupervisaoAmbientalModel::where('id_servico', $servico->id)
            ->get();

        // 1) Lotes pertencentes ao serviço
        $lotes = DB::table('supervisao_config_lotes AS scl')
            ->join('supervisao_exec_ocorrencia AS seo', 'scl.id', '=', 'seo.id_lote')
            ->where('seo.id_servico', $servico->id)
            ->distinct()
            ->orderBy('scl.nome')
            ->pluck('scl.nome')
            ->toArray();

        // 2) Registros completos do serviço (já com tipo/intensidade/status/vistoria/lote_nome)
        $registros = DB::table('supervisao_exec_ocorrencia AS seo')
            ->leftJoin(
                'supervisao_exec_ocorrencia_vistoria AS seov',
                'seov.id_ocorrencia',
                '=',
                'seo.id'
            )
            ->leftJoin(
                'supervisao_config_lotes AS scl',
                'scl.id',
                '=',
                'seo.id_lote'
            )
            ->select(
                'seo.id',
                'seo.descricao',
                'seo.tipo',
                'seo.intensidade',
                'seo.status',
                'seo.data_ocorrencia',
                DB::raw('COUNT(seov.id_ocorrencia) AS vistoria'),
                'scl.nome AS lote_nome'
            )
            ->where('seo.id_servico', $servico->id)
            ->groupBy(
                'seo.id',
                'seo.descricao',
                'seo.tipo',
                'seo.intensidade',
                'seo.status',
                'seo.data_ocorrencia',
                'scl.nome'
            )
            ->orderBy('seo.data_ocorrencia', 'desc')
            ->get();

        return [

            'lotes'    => $lotes,
            'registros' => $registros->map(fn($r) => (array) $r),
            'totalRegistros' => $allRegistros->count(),
            'getChartDataPieLocalExecOcorrencia'  => $this->getChartDataPieLocalExecOcorrencia($allRegistros),
            'getChartDataPieTipoRncDiretoExecOcorrencia' => $this->getChartDataPieTipoRncDiretoExecOcorrencia($allRegistros),
            'getChartDataPieIntensidadeExecOcorrencia' => $this->getChartDataPieIntensidadeExecOcorrencia($allRegistros),
            'getChartDataBarExecOcorrencia' => $this->getChartDataBarExecOcorrencia($servico->id),
            'getChartDataBarLoteIntensidadeExecOcorrencia' => $this->getChartDataBarLoteIntensidadeExecOcorrencia($servico->id)
        ];
    }

    private function getChartDataPieLocalExecOcorrencia($allRegistros): array
    {

        $localCounts = $allRegistros
            ->groupBy('local')
            ->map(function ($registros, $local) {
                return [
                    'local' => $local ?: 'Sem Local',
                    'total' => $registros->count(),
                ];
            })
            ->values();


        return [
            'labels' => $localCounts->pluck('local')->toArray(),
            'datasets' => [
                [
                    'data'            => $localCounts->pluck('total')->toArray(),
                    'backgroundColor' => ['#a6c48a', '#7d9c6d', '#b3c99c', '#d5dfb3'],
                    'borderColor'     => '#ffffff',
                    'borderWidth'     => 2,
                ],
            ],
        ];
    }

    private function getChartDataPieTipoRncDiretoExecOcorrencia($allRegistros): array
    {

        $tipoRncCounts = $allRegistros
            ->groupBy(function ($registro) {
                $tipo = $registro->tipo ?: 'Sem Tipo';
                $rnc  = $registro->rnc_direto ?: 'N/A';
                return "{$tipo} - {$rnc}";
            })
            ->map(function ($registros, string $tipoRnc) {
                return [
                    'tipo_rnc' => $tipoRnc,
                    'total'    => $registros->count(),
                ];
            })
            ->values();


        return [
            'labels'   => $tipoRncCounts->pluck('tipo_rnc')->toArray(),
            'datasets' => [
                [
                    'data'            => $tipoRncCounts->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#a6c48a',
                        '#7d9c6d',
                        '#b3c99c',
                        '#d5dfb3',
                    ],
                    'borderColor'     => '#ffffff',
                    'borderWidth'     => 2,
                ],
            ],
        ];
    }

    private function getChartDataPieIntensidadeExecOcorrencia($allRegistros): array
    {

        $intensidadeCounts = $allRegistros
            ->groupBy(function ($registro) {
                return $registro->intensidade ?: 'N/A';
            })
            ->map(function ($registros, string $intensidade) {
                return [
                    'intensidade' => $intensidade,
                    'total'       => $registros->count(),
                ];
            })
            ->values();


        return [
            'labels'   => $intensidadeCounts->pluck('intensidade')->toArray(),
            'datasets' => [
                [
                    'data'            => $intensidadeCounts->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#a6c48a',
                        '#7d9c6d',
                        '#b3c99c',
                        '#d5dfb3',
                    ],
                    'borderColor'     => '#ffffff',
                    'borderWidth'     => 2,
                ],
            ],
        ];
    }

    public function getChartDataBarExecOcorrencia(int $idServico): array
    {
        // Busca os registros já agrupados por mês/ano
        $allRegistros = DB::table('supervisao_exec_ocorrencia')
            ->selectRaw("concat(MONTH(data_ocorrencia), '/', YEAR(data_ocorrencia)) as dt_ocorrencia, COUNT(*) as total")
            ->where('id_servico', $idServico)
            ->groupByRaw("concat(MONTH(data_ocorrencia), '/', YEAR(data_ocorrencia))")
            ->orderByRaw("YEAR(data_ocorrencia), MONTH(data_ocorrencia)")
            ->get();

        return [
            'labels'   => $allRegistros->pluck('dt_ocorrencia')->toArray(),
            'datasets' => [
                [
                    'label'           => 'Ocorrências',
                    'data'            => $allRegistros->pluck('total')->toArray(),
                    'backgroundColor' => '#28a745',  // verde
                    'borderRadius'    => 5,
                ],
            ],
        ];
    }

    private function getChartDataBarLoteIntensidadeExecOcorrencia(int $idServico): array
    {
        // 1) Puxa os totais por lote + intensidade
        $registros = DB::table('supervisao_exec_ocorrencia AS seo')
            ->join('supervisao_config_lotes AS scl', 'scl.id', '=', 'seo.id_lote')
            ->select('scl.nome', 'seo.intensidade', DB::raw('COUNT(*) AS total'))
            ->where('seo.id_servico', $idServico)
            ->where('seo.tipo', 'RNC')
            ->groupBy('scl.nome', 'seo.intensidade')
            ->orderBy('scl.nome')
            ->get();

        // 2) Lista única de lotes (labels)
        $lotes = $registros->pluck('nome')->unique()->values();

        // 3) Intensidades fixas e mapeamento de cores
        $intensidades = ['Grave', 'Leve', 'Moderada'];
        $colors = [
            'Grave'    => '#dc3545',
            'Leve'     => '#007bff',
            'Moderada' => '#ffc107',
        ];

        // 4) Monta o dataset para cada intensidade
        $datasets = [];
        foreach ($intensidades as $intensidade) {
            $data = [];
            foreach ($lotes as $lote) {
                // busca registro correspondente ou zera
                $item = $registros
                    ->first(fn($r) => $r->nome === $lote && $r->intensidade === $intensidade);
                $data[] = $item->total ?? 0;
            }
            $datasets[] = [
                'label'           => $intensidade,
                'data'            => $data,
                'backgroundColor' => $colors[$intensidade],
                'borderRadius'    => 5,
            ];
        }

        return [
            'labels'   => $lotes->toArray(),
            'datasets' => $datasets,
        ];
    }

    // private function getChartDataPieFormaRegistro($allRegistros): array
    // {

    //     $idForma = 3;


    //     $totalRegistros = $allRegistros->count();

    //     $remocaoGroup = $allRegistros
    //         ->filter(fn($registro) => optional($registro->formaRegistro)->id === $idForma);

    //     $remocaoCount = $remocaoGroup->count();

    //     $remocaoNome = $remocaoGroup->first()->formaRegistro->nome
    //         ?? 'Remoção';

    //     $percentage = $totalRegistros > 0
    //         ? ($remocaoCount * 100) / $totalRegistros
    //         : 0;

    //     return [[
    //         'id'    => $idForma,
    //         'nome'  => $remocaoNome,
    //         'total' => round($percentage, 2),
    //     ]];
    // }




    // private function getChartDataPieDiversidade($allRegistros): array
    // {
    //     $diversidade = $allRegistros->groupBy(function ($registro) {
    //         return $registro->grupo_faunistico
    //             ? $registro->grupo_faunistico->nome
    //             : 'Sem Grupo';
    //     })->map(function ($grupoRegistros, $grupoNome) {
    //         $uniqueSpecies = $grupoRegistros->pluck('especie')
    //             ->map(function ($especie) {
    //                 // Remove somente os caracteres "." e espaço que estejam no final da string
    //                 return rtrim($especie, '. ');
    //             })
    //             ->unique();

    //         return [
    //             'grupo_faunistico' => $grupoNome,
    //             'total'            => $uniqueSpecies->count(),
    //         ];
    //     })->values();


    //     return [
    //         'labels' => $diversidade->pluck('grupo_faunistico')->toArray(),
    //         'datasets' => [
    //             [
    //                 'data' => $diversidade->pluck('total')->toArray(),
    //                 'backgroundColor' => ["#a6c48a", "#7d9c6d", "#b3c99c", "#d5dfb3"],
    //                 'borderColor' => "#ffffff",
    //                 'borderWidth' => 2,
    //             ],
    //         ],
    //     ];
    // }

    // private function getChartDataPieTipoRegistro($allRegistros): array
    // {
    //     $tipoRegistro = $allRegistros->groupBy(function ($registro) {
    //         return $registro->tipoRegistro
    //             ? $registro->tipoRegistro->nome
    //             : 'Sem Tipo';
    //     })->map(function ($grupoRegistros, $tipoNome) {
    //         return [
    //             'tipo_registro' => $tipoNome,
    //             'total'         => $grupoRegistros->count(),
    //         ];
    //     })->values();

    //     return [
    //         'labels'   => $tipoRegistro->pluck('tipo_registro')->toArray(),
    //         'datasets' => [
    //             [
    //                 'data'            => $tipoRegistro->pluck('total')->toArray(),
    //                 'backgroundColor' => ["#a6c48a", "#7d9c6d", "#b3c99c", "#d5dfb3"],
    //                 'borderColor'     => "#ffffff",
    //                 'borderWidth'     => 2,
    //             ],
    //         ],
    //     ];
    // }

    // private function getChartDataPieFormaRegistroGrafico($allRegistros): array
    // {
    //     $formaRegistro = $allRegistros->groupBy(function ($registro) {
    //         return $registro->formaRegistro
    //             ? $registro->formaRegistro->nome
    //             : 'Sem Forma';
    //     })->map(function ($grupoRegistros, $formaNome) {
    //         return [
    //             'forma_registro' => $formaNome,
    //             'total'          => $grupoRegistros->count(),
    //         ];
    //     })->values();

    //     return [
    //         'labels'   => $formaRegistro->pluck('forma_registro')->toArray(),
    //         'datasets' => [
    //             [
    //                 'data'            => $formaRegistro->pluck('total')->toArray(),
    //                 'backgroundColor' => ["#a6c48a", "#7d9c6d", "#b3c99c", "#d5dfb3"],
    //                 'borderColor'     => "#ffffff",
    //                 'borderWidth'     => 2,
    //             ],
    //         ],
    //     ];
    // }

    // private function getChartDataBar2($especiesGroup): array
    // {
    //     // Ordena os grupos de acordo com a contagem de ocorrências (do maior para o menor)
    //     $sortedGroup = $especiesGroup->sortByDesc(function ($grupo) {
    //         return $grupo->count();
    //     });

    //     return [
    //         'labels' => $sortedGroup->keys()->toArray(),
    //         'datasets' => [
    //             [
    //                 'label' => 'Ocorrências',
    //                 'data' => $sortedGroup->map(function ($grupo) {
    //                     return $grupo->count();
    //                 })->values()->toArray(),
    //                 'backgroundColor' => "rgba(30, 144, 255, 0.8)",
    //                 'borderColor' => "rgba(30, 144, 255, 1)",
    //                 'borderWidth' => 1,
    //             ],
    //         ],
    //     ];
    // }
}
