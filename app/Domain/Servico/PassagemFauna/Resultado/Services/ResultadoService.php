<?php

namespace App\Domain\Servico\PassagemFauna\Resultado\Services;

use App\Models\ServicoPassagemFaunaExecCampanha;
use App\Models\ServicoPassagemFaunaResultado;
use App\Models\ServicoPassagemFaunaResultadoAnalise;
use App\Models\ServicoPassagemFaunaResultadoOutrasAnalise;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class ResultadoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPassagemFaunaResultado::class;
    protected string $modelClassAnalise = ServicoPassagemFaunaResultadoAnalise::class;
    protected string $modelClassOutraAnalise = ServicoPassagemFaunaResultadoOutrasAnalise::class;

    public function index($servico, array $searchParams): array
    {
        return [
            'resultados' => $this->searchAllColumns(...$searchParams)
                ->with(['campanhas'])
                ->where('id_servico', '=', $servico->id)
                ->paginate()
                ->appends($searchParams),
            'campanhas' => ServicoPassagemFaunaExecCampanha::where('id_servico', '=', $servico->id)->get()
        ];
    }

    public function resultado(object $resultado)
    {
        $resultado->load([
            'outras_analises',
            'analise',
            'campanhas.registros.status_iunc',
            'campanhas.registros.status_federal',
            'campanhas.registros.passagem'
        ]);

        $registros = $resultado->campanhas->flatMap(function ($campanha) {
            return $campanha->registros;
        });

        $classes = $registros->countBy(function ($item) {
            return $item->classe;
        });

        $tipos = $registros->countBy(function ($item) {
            return $item->tipo;
        });

        $passagens = $registros->countBy(function ($item) {
            return $item->passagem['nome_id'];
        });

        $especies = $registros->countBy(function ($item) {
            return $item->especie;
        });

        $colors = $classes->map(function () {
            return $this->getRandomColor();
        })->toArray();

        $chartDataClasses = [
            'labels' => $classes->keys()->toArray(),
            'datasets' => [
                [
                    'label' => 'Qtd',
                    'data' => $classes->values()->toArray(),
                    'backgroundColor' => $colors
                ]
            ]
        ];

        $colors = $classes->map(function () {
            return $this->getRandomColor();
        })->toArray();

        $chartDataTipos = [
            'labels' => $tipos->keys()->toArray(),
            'datasets' => [
                [
                    'label' => 'Qtd',
                    'data' => $tipos->values()->toArray(),
                    'backgroundColor' => $colors
                ]
            ]
        ];

        $colors = $passagens->map(function () {
            return $this->getRandomColor();
        })->toArray();

        $chartDataPassagens = [
            'labels' => $passagens->keys()->toArray(),
            'datasets' => [
                [
                    'label' => 'Qtd',
                    'data' => $passagens->values()->toArray(),
                    'backgroundColor' => $colors
                ]
            ]
        ];

        $colors = $especies->map(function () {
            return $this->getRandomColor();
        })->toArray();

        $chartDataEspecies = [
            'labels' => $especies->keys()->toArray(),
            'datasets' => [
                [
                    'label' => 'Qtd',
                    'data' => $especies->values()->toArray(),
                    'backgroundColor' => $colors
                ]
            ]
        ];

        $chartDataCampanhas = [
            'labels' => ['Campanhas'],
            'datasets' => $resultado->campanhas->map(function ($campanha) {
                return [
                    'label' => $campanha->id,
                    'data' => [count($campanha->registros)],
                    'backgroundColor' => $this->getRandomColor()
                ];
            })->toArray()
        ];

        $chartDatas = [
            'classes' => $chartDataClasses,
            'campanhas' => $chartDataCampanhas,
            'tipos' => $chartDataTipos,
            'passagens' => $chartDataPassagens,
            'especies' => $chartDataEspecies
        ];

        return [
            'resultado' => $resultado,
            'chartDatas' => $chartDatas
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
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $post);

        $response['model']->campanhas()->sync(collect($post['campanhas_selecionadas'])->pluck('id'));

        return $response;
    }

    public function update(array $post): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);

        $resultado = $this->modelClass::findOrFail($post['id']);

        $resultado->campanhas()->sync(collect($post['campanhas_selecionadas'])->pluck('id'));

        return $response;
    }

    public function destroy(object $resultado): array
    {
        $resultado->campanhas()->sync(collect([]));

        return $this->dataManagement->delete(entity: $this->modelClass, id: $resultado->id);
    }

    public function destroyOutraAnalise(object $outra_analise): array
    {
        Storage::delete($outra_analise->caminho_arquivo);

        return $this->dataManagement->delete(entity: $this->modelClassOutraAnalise, id: $outra_analise->id);
    }

    public function storeAnalise(array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClassAnalise, infos: $post);
    }

    public function updateAnalise(array $post)
    {
        if (array_key_exists('graf_reg_classe', $post)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $post['graf_reg_classe']);

            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_classe']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'PassagemFauna' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_classe'] = str_replace("public\\", "", $path);
        } elseif (array_key_exists('graf_reg_campanha', $post)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $post['graf_reg_campanha']);

            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_campanha']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'PassagemFauna' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_campanha'] = str_replace("public\\", "", $path);
        } elseif (array_key_exists('graf_reg_tipo', $post)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $post['graf_reg_tipo']);

            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_tipo']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'PassagemFauna' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_tipo'] = str_replace("public\\", "", $path);
        } elseif (array_key_exists('graf_reg_passagem', $post)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $post['graf_reg_passagem']);

            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_passagem']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'PassagemFauna' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_passagem'] = str_replace("public\\", "", $path);
        } elseif (array_key_exists('graf_reg_especie', $post)) {
            Storage::delete('public' . DIRECTORY_SEPARATOR . $post['graf_reg_especie']);

            $image = str_replace('data:image/png;base64,', '', $post['graf_reg_especie']);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            $path = 'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'PassagemFauna' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'Analise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $post['id_resultado'] . '.png';
            Storage::disk()->put($path, $imageData);

            $post['graf_reg_especie'] = str_replace("public\\", "", $path);
        }

        return $this->dataManagement->update(entity: $this->modelClassAnalise, infos: $post, id: $post['id']);
    }

    public function storeOutraAnalise(array $post): array
    {
        $nome = $post['arquivo']->getClientOriginalName();
        $tipo = $post['arquivo']->extension();
        $caminho = $post['arquivo']->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'PassagemFauna' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'OutraAnalise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

        return $this->dataManagement->create(entity: $this->modelClassOutraAnalise, infos: [
            'id_resultado' => $post['id_resultado'],
            'nome' => $post['nome'],
            'analise' => $post['analise'],
            'caminho_arquivo' => str_replace("public\\", "", $caminho),
            'extensao' => $tipo,
        ]);
    }

    public function updateOutraAnalise(array $post): array
    {
        if ($post['caminho_arquivo']) {
            Storage::delete($post['caminho_arquivo']);
        }

        $nome = $post['arquivo']->getClientOriginalName();
        $tipo = $post['arquivo']->extension();
        $caminho = $post['arquivo']->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'PassagemFauna' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'OutraAnalise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

        return $this->dataManagement->update(entity: $this->modelClassOutraAnalise, infos: [
            'id_resultado' => $post['id_resultado'],
            'nome' => $post['nome'],
            'analise' => $post['analise'],
            'caminho_arquivo' => str_replace("public\\", "", $caminho),
            'extensao' => $tipo,
        ], id: $post['id']);
    }
}
