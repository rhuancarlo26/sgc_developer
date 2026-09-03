<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Services;

use App\Models\ServicoPmqaParametro;
use App\Models\ServicoPmqaResultado;
use App\Models\ServicoPmqaResultadoAnaliseIqa;
use App\Models\SgcPmqaResultadoAnaliseParametro;
use App\Models\ServicoPmqaResultadoOutraAnalise;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaCampanha;
use App\Models\SgcPmqaResultado;
use App\Models\SgcPmqaResultadoAnaliseIqa;
use App\Models\SgcPmqaResultadoOutraAnalise;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class ResultadoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = SgcPmqaResultado::class;
    protected string $modelClassAnalise = SgcPmqaResultadoAnaliseParametro::class;
    protected string $modelClassAnaliseIqa = SgcPmqaResultadoAnaliseIqa::class;
    protected string $modelClassOutraAnalise = SgcPmqaResultadoOutraAnalise::class;

    public function index(SgcPmqa $pmqa, array $searchParams): array
    {
        $resultados = $this->searchAllColumns(...$searchParams)
            ->with(['campanhas'])
            ->where('pmqa_id', $pmqa->id)
            ->paginate()
            ->appends($searchParams);

        $campanhas = SgcPmqaCampanha::where('pmqa_id', $pmqa->id)->get();

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
        $path = 'public' . DIRECTORY_SEPARATOR . 'SgcPmqa' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $request['sgc_resultado_id'] . '_' . $request['parametro_id'] . '.png';
        Storage::disk()->put($path, $request['graf_analise_parametro']);

        return $this->dataManagement->create(entity: $this->modelClassAnalise, infos: [
            ...$request,
            'graf_analise_parametro' => str_replace("public\\", "", $path)
        ]);
    }

    public function storeAnaliseIqa(array $request): array
    {
        $path = 'public' . DIRECTORY_SEPARATOR . 'SgcPmqa' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_iqa_' . $request['sgc_resultado_id'] . '.png';
        Storage::disk()->put($path, $request['graf_analise_iqa']);

        return $this->dataManagement->create(entity: $this->modelClassAnaliseIqa, infos: [
            ...$request,
            'graf_analise_iqa' => str_replace("public\\", "", $path)
        ]);
    }

    public function storeOutraAnalise(array $request): array
    {
        if ($request['arquivo']->isvalid()) {
            $nome = $request['arquivo']->getClientOriginalName();
            $tipo = $request['arquivo']->extension();
            $caminho = $request['arquivo']->storeAs('public' . DIRECTORY_SEPARATOR . 'SgcPmqa' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'OutraAnalise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);
        }

        return $this->dataManagement->create(entity: $this->modelClassOutraAnalise, infos: [
            'sgc_resultado_id' => $request['sgc_resultado_id'],
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
            $caminho = $request['arquivo']->storeAs('public' . DIRECTORY_SEPARATOR . 'SgcPmqa' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'OutraAnalise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);
        }

        return $this->dataManagement->update(entity: $this->modelClassOutraAnalise, infos: [
            'sgc_resultado_id' => $request['sgc_resultado_id'],
            'tipo' => $tipo,
            'caminho_arquivo' => str_replace("public\\", "", $caminho),
            'nome' => $request['nome'],
            'analise' => $request['analise']
        ], id: $request['id']);
    }

    public function updateAnalises(array $request): array
    {
        $analise = $this->modelClassAnalise::where('sgc_resultado_id', $request['sgc_resultado_id'])->where('parametro_id', $request['parametro_id'])->first();

        if (isset($analise->graf_analise_parametro)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $analise->graf_analise_parametro);
        }

        $path = 'public' . DIRECTORY_SEPARATOR . 'SgcPmqa' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $request['sgc_resultado_id'] . '_' . $request['parametro_id'] . '.png';
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
        $analiseIqa = ServicoPmqaResultadoAnaliseIqa::where('sgc_resultado_id', $request['sgc_resultado_id'])->first();

        if (isset($analiseIqa->graf_analise_iqa)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $analiseIqa->graf_analise_iqa);
        }

        $path = 'public' . DIRECTORY_SEPARATOR . 'SgcPmqa' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_iqa_' . $request['sgc_resultado_id'] . '.png';
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
            'campanhas'
        ]);

        $chartDataIqa = [
            'labels' => [],
            'datasets' => []
        ];

        $medicao       = true;
        $justificativa = '';
        foreach ($resultado->campanhas as $campanha) {
            $iqas = [];

            foreach ($campanha->campanha_pontos as $campanhaPonto) {
                $chartDataIqa['labels'][] = $campanhaPonto->ponto->nome_ponto_coleta;
                if (isset($campanhaPonto->medicao) && $campanhaPonto->medicao->medido) {
                    $medicao = false;
                    $justificativa = $campanhaPonto->medicao->observacao;
                }

                if (isset($campanhaPonto->medicao)) {
                    $id = $campanhaPonto->medicao->id;
                    $iqa = $campanhaPonto->medicao->iqa;

                    if (!isset($iqas[$id])) {
                        if ($iqa) {
                            $iqas[$id] = (float) $iqa;
                        }
                    }
                }
            }
            $chartDataIqa['datasets'][] = [
                'label' => $campanha->nome_campanha,
                'backgroundColor' => '#' . substr(md5($campanha->nome_campanha), 0, 6),
                'data' => array_values($iqas)
            ];
        }

        $parametrosIds = collect($resultado->campanhas)->flatMap(function ($campanha) {
            return collect($campanha->campanha_pontos)->flatMap(function ($campanhaPonto) {
                if (isset($campanhaPonto->ponto) && isset($campanhaPonto->ponto->lista)) {
                    return collect($campanhaPonto->ponto->lista->parametros)->pluck('id');
                }
                return collect();
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
                                            return $medicaoParametro->parametro_id == $parametro->id;
                                        })
                                        ->pluck('medicao')
                                        ->map(function($val) { return (float) $val; })
                                        ->toArray();
                                }
                                return [
                                    'id' => $campanhaPonto->ponto->id,
                                    'label' => $campanhaPonto->ponto->nome_ponto_coleta,
                                    'backgroundColor' => '#' . substr(md5($campanhaPonto->ponto->nome_ponto_coleta), 0, 6),
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
        return [
            'parametros' => $parametros,
            'resultado' => $resultado,
            'uniqueParametros' => $uniqueParametros,
            'chartDataIqa' => $chartDataIqa,
            'medicao' => [
                'medido' => $medicao,
                'justificativa' => $justificativa
            ]
        ];
    }
}
