<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Services;

use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervicaoExecOcorrenciaRegistro;
use App\Models\ServicoConOcorrSupervisaoExecAca;
use App\Models\ServicoConOcorrSupervisaoResultado;
use App\Models\ServicoConOcorrSupervisaoResultadoAnalise;
use App\Models\ServicoConOcorrSupervisaoResultadoOutrasAnalises;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Carbon\Carbon;
use FontLib\Table\Type\post;
use Illuminate\Support\Facades\Storage;

class ResultadoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoConOcorrSupervisaoResultado::class;
    protected string $modelClassAnalise = ServicoConOcorrSupervisaoResultadoAnalise::class;
    protected string $modelClassOutraAnalise = ServicoConOcorrSupervisaoResultadoOutrasAnalises::class;

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

    public function storeOutraAnalise(array $post): array
    {
        $nome = $post['arquivo']->getClientOriginalName();
        $tipo = $post['arquivo']->extension();
        $caminho = $post['arquivo']->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'OutraAnalise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

        return $this->dataManagement->create(entity: $this->modelClassOutraAnalise, infos: [
            'id_resultado' => $post['id_resultado'],
            'nome' => $post['nome'],
            'analise' => $post['analise'],
            'caminho_arquivo' => str_replace("public\\", "", $caminho),
            'extensao' => $tipo,
        ]);
    }

    public function storeAnalise(array $post): array
    {
        if (array_key_exists('graf_reg_intensidade', $post)) {
            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_intensidade']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_intensidade'] = str_replace("public\\", "", $path);
        } elseif (array_key_exists('graf_reg_classificacao', $post)) {
            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_classificacao']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_classificacao'] = str_replace("public\\", "", $path);
        } elseif (array_key_exists('graf_reg_local', $post)) {
            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_local']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_local'] = str_replace("public\\", "", $path);
        } elseif (array_key_exists('graf_reg_lote', $post)) {
            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_lote']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_lote'] = str_replace("public\\", "", $path);
        }

        return $this->dataManagement->create(entity: $this->modelClassAnalise, infos: $post);
    }

    public function update(array $post): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
    }

    public function updateAnalise(array $post): array
    {
        if (array_key_exists('graf_reg_intensidade', $post)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $post['graf_reg_intensidade']);

            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_intensidade']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_intensidade'] = str_replace("public\\", "", $path);
        } elseif (array_key_exists('graf_reg_classificacao', $post)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $post['graf_reg_classificacao']);

            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_classificacao']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_classificacao'] = str_replace("public\\", "", $path);
        } elseif (array_key_exists('graf_reg_local', $post)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $post['graf_reg_local']);

            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_local']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_local'] = str_replace("public\\", "", $path);
        } elseif (array_key_exists('graf_reg_lote', $post)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $post['graf_reg_lote']);

            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_lote']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_lote'] = str_replace("public\\", "", $path);
        }

        return $this->dataManagement->update(entity: $this->modelClassAnalise, infos: $post, id: $post['id']);
    }

    public function updateOutraAnalise(array $post): array
    {
        Storage::delete('public' . DIRECTORY_SEPARATOR . $post['caminho_arquivo']);

        $nome = $post['arquivo']->getClientOriginalName();
        $tipo = $post['arquivo']->extension();
        $caminho = $post['arquivo']->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'OutraAnalise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

        return $this->dataManagement->update(entity: $this->modelClassOutraAnalise, infos: [
            'id_resultado' => $post['id_resultado'],
            'nome' => $post['nome'],
            'analise' => $post['analise'],
            'caminho_arquivo' => str_replace("public\\", "", $caminho),
            'extensao' => $tipo,
        ], id: $post['id']);
    }

    public function destroy(int $resultado_id): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $resultado_id);
    }

    public function destroyOutraAnalise($outra_analise): array
    {
        Storage::delete('public' . DIRECTORY_SEPARATOR . $outra_analise->caminho_arquivo);

        return $this->dataManagement->delete(entity: $this->modelClassOutraAnalise, id: $outra_analise->id);
    }
}
