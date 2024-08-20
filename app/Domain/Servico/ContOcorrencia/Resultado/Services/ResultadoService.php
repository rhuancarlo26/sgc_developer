<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Services;

use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervisaoExecAca;
use App\Models\ServicoConOcorrSupervisaoResultado;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Carbon\Carbon;

class ResultadoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoConOcorrSupervisaoResultado::class;

    public function index(int $servico_id, array $searchParams): array
    {
        return [
            'resultados' => $this->searchAllColumns(...$searchParams)
                ->where('id_servico', $servico_id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function resultado($resultado)
    {
        $collection = ServicoConOcorrOcorrenciSupervisaoExecOcorrencia::with([
            'lote' => function ($q) {
                $q->select(['id', 'nome_id', 'empresa']);
            }
        ])->where('id_servico', $resultado->id_servico)->get();

        $startDate = Carbon::parse($resultado->dt_inicio);
        $endDate = Carbon::parse($resultado->dt_fim);

        $filtered = $collection->filter(function ($item) use ($startDate, $endDate) {
            return Carbon::parse($item->created_at)->between($startDate, $endDate);
        })->values();

        $acas = ServicoConOcorrSupervisaoExecAca::with([
            'rncs' => function ($q) {
                $q->select(['nome_id', 'local', 'classificacao']);
            },
            'lote' => function ($q) {
                $q->select(['id', 'nome_id', 'empresa']);
            }
        ])->where('id_servico', $resultado->id_servico)->get();

        $acaFiltered = $acas->filter(function ($item) use ($startDate, $endDate) {
            return Carbon::parse($item->created_at)->between($startDate, $endDate);
        })->values();

        $grouped = $collection->groupBy(function ($item) {
            return "{$item->tipo}_{$item->status}";
        });

        $intensidade = $collection
            ->where(key: 'tipo', value: 'RNC')
            //->where('status', value: 'Não Atendida')
            ->groupBy(function ($item) {
                return $item->intensidade;
            });

        $local = $collection
            ->where(key: 'tipo', value: 'RNC')
            //->where('status', value: 'Não Atendida')
            ->groupBy(function ($item) {
                return "{$item->intensidade}_{$item->local}";
            });

        $classificacao = $collection
            ->where(key: 'tipo', value: 'RNC')
            //->where('status', value: 'Não Atendida')
            ->groupBy(function ($item) {
                return "{$item->intensidade}_{$item->classificacao}";
            });

        $lote = $collection
            ->where(key: 'tipo', value: 'RNC')
            //->where('status', value: 'Não Atendida')
            ->groupBy(function ($item) {
                return "{$item->intensidade}_{$item->lote['nome_id']} - {$item->lote['empresa']}";
            });

        $groupedLocal = $local->mapWithKeys(function ($value, $key) {
            list($category, $subcategory) = explode('_', $key);
            return ["$category.$subcategory" => 1];
        })->reduce(function ($carry, $item, $key) {
            list($category, $subcategory) = explode('.', $key);
            if (!isset($carry[$category])) {
                $carry[$category] = [];
            }
            if (!isset($carry[$category][$subcategory])) {
                $carry[$category][$subcategory] = 0;
            }
            $carry[$category][$subcategory] += $item;
            return $carry;
        }, []);

        $subcategories = [];
        foreach ($groupedLocal as $category => $subcat) {
            $subcategories = array_merge($subcategories, array_keys($subcat));
        }
        $subcategories = array_unique($subcategories);

        $chartData = [
            'labels' => ['Leve', 'Moderada', 'Grave'],
            'datasets' => []
        ];

        foreach ($subcategories as $subcategory) {
            $data = [];
            foreach (['Leve', 'Moderada', 'Grave'] as $category) {
                $data[] = $groupedLocal[$category][$subcategory] ?? 0;
            }

            $chartData['datasets'][] = [
                'label' => $subcategory,
                'backgroundColor' => $this->getRandomColor(),
                'data' => $data
            ];
        }

        $groupedClassificacao = $classificacao->mapWithKeys(function ($value, $key) {
            list($category, $subcategory) = explode('_', $key);
            return ["$category.$subcategory" => 1];
        })->reduce(function ($carry, $item, $key) {
            list($category, $subcategory) = explode('.', $key);
            if (!isset($carry[$category])) {
                $carry[$category] = [];
            }
            if (!isset($carry[$category][$subcategory])) {
                $carry[$category][$subcategory] = 0;
            }
            $carry[$category][$subcategory] += $item;
            return $carry;
        }, []);

        $subcategoriesClass = [];
        foreach ($groupedClassificacao as $category => $subcat) {
            $subcategoriesClass = array_merge($subcategoriesClass, array_keys($subcat));
        }
        $subcategoriesClass = array_unique($subcategoriesClass);

        $chartDataClass = [
            'labels' => ['Leve', 'Moderada', 'Grave'],
            'datasets' => []
        ];

        foreach ($subcategoriesClass as $subcategory) {
            $data = [];
            foreach (['Leve', 'Moderada', 'Grave'] as $category) {
                $data[] = $groupedClassificacao[$category][$subcategory] ?? 0;
            }

            $chartDataClass['datasets'][] = [
                'label' => $subcategory,
                'backgroundColor' => $this->getRandomColor(),
                'data' => $data
            ];
        }

        $groupedLote = $lote->mapWithKeys(function ($value, $key) {
            list($category, $subcategory) = explode('_', $key);
            return ["$category///$subcategory" => 1];
        })->reduce(function ($carry, $item, $key) {
            list($category, $subcategory) = explode('///', $key);
            if (!isset($carry[$category])) {
                $carry[$category] = [];
            }
            if (!isset($carry[$category][$subcategory])) {
                $carry[$category][$subcategory] = 0;
            }
            $carry[$category][$subcategory] += $item;
            return $carry;
        }, []);

        $subcategoriesLote = [];
        foreach ($groupedLote as $category => $subcat) {
            $subcategoriesLote = array_merge($subcategoriesLote, array_keys($subcat));
        }
        $subcategoriesLote = array_unique($subcategoriesLote);

        $chartDataLote = [
            'labels' => ['Leve', 'Moderada', 'Grave'],
            'datasets' => []
        ];

        foreach ($subcategoriesLote as $subcategory) {
            $data = [];
            foreach (['Leve', 'Moderada', 'Grave'] as $category) {
                $data[] = $groupedLote[$category][$subcategory] ?? 0;
            }

            $chartDataLote['datasets'][] = [
                'label' => $subcategory,
                'backgroundColor' => $this->getRandomColor(),
                'data' => $data
            ];
        }

        return [
            'tabs' => [
                'roa_atendido' => $grouped->get('ROA_Atendida', collect()),
                'roa_aberto' => $grouped->get('ROA_Em aberto', collect()),
                'rnc_atendido' => $grouped->get('RNC_Atendida', collect()),
                'rnc_aberto' => $grouped->get('RNC_Em aberto', collect()),
                'intensidades' => [
                    'labels' => ['Leve', 'Moderada', 'Grave'],
                    'datasets' => [
                        [
                            'backgroundColor' => [
                                $this->getRandomColor(),
                                $this->getRandomColor(),
                                $this->getRandomColor()
                            ],
                            'data' => [
                                count($intensidade->get('Leve', collect())),
                                count($intensidade->get('Moderada', collect())),
                                count($intensidade->get('Grave', collect())),
                            ]
                        ]
                    ]
                ],
                'locais' => $chartData,
                'classificacoes' => $chartDataClass,
                'lotes' => $chartDataLote,
                'acas' => $acaFiltered
            ]
        ];
    }

    public function getRandomColor()
    {
        $letters = '0123456789ABCDEF';
        $color = '#';
        for ($i = 0; $i < 6; $i++) {
            $color .= $letters[rand(0, 15)];
        }
        return $color;
    }

    public function store(array $post): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
    }

    public function update(array $post): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
    }

    public function destroy(int $resultado_id): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $resultado_id);
    }
}
