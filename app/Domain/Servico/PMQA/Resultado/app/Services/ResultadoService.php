<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Services;

use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaParametro;
use App\Models\ServicoPmqaResultado;
use App\Models\ServicoPmqaResultadoAnaliseIqa;
use App\Models\ServicoPmqaResultadoAnaliseParametro;
use App\Models\ServicoPmqaResultadoOutraAnalise;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class ResultadoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPmqaResultado::class;
    protected string $modelClassAnalise = ServicoPmqaResultadoAnaliseParametro::class;
    protected string $modelClassAnaliseIqa = ServicoPmqaResultadoAnaliseIqa::class;
    protected string $modelClassOutraAnalise = ServicoPmqaResultadoOutraAnalise::class;

    public function index(Servicos $servico, array $searchParams): array
    {
        $resultados = $this->searchAllColumns(...$searchParams)
            ->with(['campanhas'])
            ->where('fk_servico', $servico->id)
            ->paginate()
            ->appends($searchParams);

        $campanhas = ServicoPmqaCampanha::where('fk_servico', $servico->id)->get();

        return [
            'resultados' => $resultados,
            'campanhas' => $campanhas
        ];
    }

    public function store(array $request): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

        $response['model']->campanhas()->sync(collect($request['campanhas_selecionadas'])->pluck('id'));

        return $response;
    }

    public function storeAnalises(array $request): array
    {
        $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $request['resultado_id'] . '_' . $request['parametro_id'] . '.png';
        Storage::disk()->put($path, $request['imagem']);

        return $this->dataManagement->create(entity: $this->modelClassAnalise, infos: [
            ...$request,
            'caminho' => str_replace("public\\", "", $path)
        ]);
    }

    public function storeAnaliseIqa(array $request): array
    {
        $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_iqa_' . $request['resultado_id'] . '.png';
        Storage::disk()->put($path, $request['imagem']);

        return $this->dataManagement->create(entity: $this->modelClassAnaliseIqa, infos: [
            ...$request,
            'caminho' => str_replace("public\\", "", $path)
        ]);
    }

    public function storeOutraAnalise(array $request): array
    {
        if ($request['arquivo']->isvalid()) {
            $nome = $request['arquivo']->getClientOriginalName();
            $tipo = $request['arquivo']->extension();
            $caminho = $request['arquivo']->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'OutraAnalise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);
        }

        return $this->dataManagement->create(entity: $this->modelClassOutraAnalise, infos: [
            'fk_resultado' => $request['fk_resultado'],
            'nome' => $request['nome'],
            'extensao' => $tipo,
            'caminho_arquivo' => str_replace("public\\", "", $caminho),
            'analise' => $request['analise']
        ]);
    }

    public function updateOutraAnalise(array $request): array
    {
        if ($outraAnalise = ServicoPmqaResultadoOutraAnalise::find($request['id'])) {
            if ($outraAnalise->caminho) {
                Storage::delete('public' . DIRECTORY_SEPARATOR . $outraAnalise->caminho);
            }
        }

        if ($request['arquivo']->isvalid()) {
            $nome = $request['arquivo']->getClientOriginalName();
            $tipo = $request['arquivo']->extension();
            $caminho = $request['arquivo']->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'OutraAnalise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);
        }

        return $this->dataManagement->update(entity: $this->modelClassOutraAnalise, infos: [
            'fk_resultado' => $request['fk_resultado'],
            'tipo' => $tipo,
            'caminho_arquivo' => str_replace("public\\", "", $caminho),
            'nome' => $request['nome'],
            'analise' => $request['analise']
        ], id: $request['id']);
    }

    public function updateAnalises(array $request): array
    {
        $analise = $this->modelClassAnalise::where('fk_resultado', $request['fk_resultado'])->where('fk_parametro', $request['fk_parametro'])->first();

        if (isset($analise->graf_analise_parametro)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $analise->graf_analise_parametro);
        }

        $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $request['fk_resultado'] . '_' . $request['fk_parametro'] . '.png';
        Storage::disk()->put($path, $request['graf_analise_parametro']);

        if ($analise) {
            $response = $this->dataManagement->update(entity: $this->modelClassAnalise, infos: [
                ...$request,
                'graf_analise_parametro' => str_replace("public\\", "", $path)
            ], id: $analise->id);
        } else {
            $response = $this->dataManagement->create(entity: $this->modelClassAnalise, infos: [
                ...$request,
                'graf_analise_parametro' => str_replace("public\\", "", $path)
            ]);
        }

        return $response;
    }

    public function updateAnaliseIqa(array $request): array
    {
        $analiseIqa = ServicoPmqaResultadoAnaliseIqa::where('fk_resultado', $request['fk_resultado'])->first();

        if (isset($analiseIqa->graf_analise_iqa)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $analiseIqa->graf_analise_iqa);
        }

        $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_iqa_' . $request['fk_resultado'] . '.png';
        Storage::disk()->put($path, $request['graf_analise_iqa']);

        return $this->dataManagement->update(entity: $this->modelClassAnaliseIqa, infos: [
            ...$request,
            'graf_analise_iqa' => str_replace("public\\", "", $path)
        ], id: $request['id']);
    }

    public function update(array $request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

        $this->modelClass::find($request['id'])->campanhas()->sync(collect($request['campanhas_selecionadas'])->pluck('id'));

        return $response;
    }

    public function destroy(ServicoPmqaResultado $resultado): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $resultado->id);
    }

    public function destroyOutraAnalise(ServicoPmqaResultadoOutraAnalise $outra_analise): array
    {
        Storage::delete('public' . DIRECTORY_SEPARATOR . $outra_analise->caminho);

        return $this->dataManagement->delete(entity: $this->modelClassOutraAnalise, id: $outra_analise->id);
    }

    public function getRandomColor(): string
    {
        $letters = '0123456789ABCDEF';
        $color = '#';
        for ($i = 0; $i < 6; $i++) {
            $color .= $letters[rand(0, 15)];
        }
        return $color;
    }

    public function resultado($resultado): array
    {
        $parametros = ServicoPmqaParametro::all();
        $resultado->load([
            'analises',
            'analise_iqa',
            'outras_analises',
            'campanhas.medicoes.ponto_medicao',
            'campanhas.medicoes.lista_parametro.parametro_lista',
            'campanhas.pontos.lista.parametros_vinculados.medicao'
        ]);

        $chartDataIqa = [
            'labels' => [],
            'datasets' => []
        ];

        foreach ($resultado->campanhas as $campanha) {
            $iqas = [];

            foreach ($campanha->campanha_pontos as $campanhaPonto) {
                $chartDataIqa['labels'][] = $campanhaPonto->ponto->nome_ponto_coleta;

                if (isset($campanhaPonto->medicao)) {
                    $id = $campanhaPonto->medicao->id;
                    $iqa = $campanhaPonto->medicao->iqa;

                    if (!isset($iqas[$id])) {
                        if ($iqa) {
                            $iqas[$id] = $iqa;
                        }
                    }
                }
            }

            $chartDataIqa['datasets'][] = [
                'label' => $campanha->nome_campanha,
                'backgroundColor' => $this->getRandomColor(),
                'data' => array_values($iqas)
            ];


//            }
//
//            $chartDataIqa['datasets'][] = [
//                'label' => $campanha->nome,
//                'backgroundColor' => $this->getRandomColor(),
//                'data' => array_values($iqas)
//            ];
        }
//        foreach ($resultado->campanhas as $campanha) {
//            $chartDataIqa['labels'][] = $campanha->nome;
//
//            $iqas = [];
//
//            foreach ($campanha->medicoes as $medicao) {
//                $id = $medicao->ponto_medicao->id;
//                $iqa = $medicao->ponto_medicao->iqa;
//
//                if (!isset($iqas[$id])) {
//                    if ($iqa) {
//                        $iqas[$id] = $iqa;
//                    }
//                }
//            }
//
//            $chartDataIqa['datasets'][] = [
//                'label' => $campanha->nome,
//                'backgroundColor' => $this->getRandomColor(),
//                'data' => array_values($iqas)
//            ];
//        }

        $parametrosIds = collect($resultado->campanhas)->flatMap(function ($campanha) {
            return collect($campanha->campanha_pontos)->flatMap(function ($campanhaPonto) {
                if (isset($campanhaPonto->medicao)) {
                    return collect($campanhaPonto->medicao->parametros)->pluck('fk_parametro');
                }
            });
        })->unique()->toArray();

        $uniqueParametros = collect($parametros)->filter(function ($parametro) use ($parametrosIds, $resultado) {
            if (in_array($parametro->id, $parametrosIds)) {
                $datasets = collect($resultado->campanhas)
                    ->flatMap(function ($campanha) use ($parametro) {
                        return collect($campanha->campanha_pontos)
                            ->map(function ($campanhaPonto) use ($parametro) {
                                if (isset($campanhaPonto->medicao)) {
                                    $medicoes = collect($campanhaPonto->medicao->parametros)
                                        ->filter(function ($medicaoParametro) use ($parametro) {
                                            return $medicaoParametro->fk_parametro == $parametro->id;
                                        })
                                        ->pluck('medicao')
                                        ->toArray();
                                }
                                return [
                                    'label' => $campanhaPonto->ponto->nome_ponto_coleta, // Use o nome do ponto de campanha, se aplicável
                                    'backgroundColor' => $this->getRandomColor(),
                                    'data' => $medicoes ?? []
                                ];
                            });
                    })
                    ->toArray();

                $maxSize = 0;

                foreach ($datasets as $dataset) {
                    $currentSize = count($dataset['data']);
                    if ($currentSize > $maxSize) {
                        $maxSize = $currentSize;
                    }
                }

                $parametro->datasets = [
                    'labels' => range(1, $maxSize),
                    'datasets' => $datasets
                ];

                return true;
            }
            return false;
        })->keyBy('id')->toArray();

//        $uniqueParametros = collect($parametros)->filter(function ($parametro) use ($parametrosIds, $resultado) {
//            if (in_array($parametro->id, $parametrosIds)) {
//                $datasets = collect($resultado->campanhas)
//                    ->map(function ($campanha) use ($parametro) {
//                        $medicoes = collect($campanha->medicoes)
//                            ->filter(function ($medicao) use ($parametro) {
//                                return $medicao->lista_parametro->parametro_id == $parametro->id;
//                            })
//                            ->pluck('medicao')
//                            ->toArray();
//
//                        return [
//                            'label' => $campanha->nome,
//                            'backgroundColor' => $this->getRandomColor(),
//                            'data' => $medicoes
//                        ];
//                    })
//                    ->toArray();
//
//                $maxSize = 0;
//
//                foreach ($datasets as $dataset) {
//                    $currentSize = count($dataset['data']);
//                    if ($currentSize > $maxSize) {
//                        $maxSize = $currentSize;
//                    }
//                }
//
//                $parametro->datasets = [
//                    'labels' => range(1, $maxSize),
//                    'datasets' => $datasets
//                ];
//
//                return true;
//            }
//            return false;
//        })->keyBy('id')->toArray();

        return [
            'parametros' => $parametros,
            'resultado' => $resultado,
            'uniqueParametros' => $uniqueParametros,
            'chartDataIqa' => $chartDataIqa
        ];
    }
}
