<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Registro\Services;

use App\Models\ServicoMonitoraFaunaExecRegistro;
use App\Models\ServicoPassagemFaunaConfigPassagem;
use App\Models\ServicoPassagemFaunaExecCampanha;
use App\Models\ServicoPassagemFaunaExecRegistro;
use App\Models\ServicoPassagemFaunaExecRegistroImagem;
use App\Models\ServicoPassagemFaunaExecStatusConservacao;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Cast\Object_;
use App\Models\Servicos;


class RegistroService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPassagemFaunaExecRegistro::class;
    protected string $modelClassImagem = ServicoPassagemFaunaExecRegistroImagem::class;

    public function index($servico, array $searchParams): array
    {
        return [
            'registros' => $this->searchAllColumns(...$searchParams)
                ->with(['passagem', 'imagem', 'status_federal', 'status_iunc'])
                ->where('id_servico', '=', $servico->id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function create($servico)
    {
        return [
            'campanhas' => ServicoPassagemFaunaExecCampanha::where('id_servico', $servico->id)->get(),
            'passagens' => ServicoPassagemFaunaConfigPassagem::where('id_servico', $servico->id)->get(),
            'status_conservacoes' => ServicoPassagemFaunaExecStatusConservacao::all()
        ];
    }

    public function store(array $request)
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function storeArquivo(array $request)
    {
        $nome = $request['arquivo']->getClientOriginalName();
        $caminho = $request['arquivo']->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'PassagemFauna' . DIRECTORY_SEPARATOR . 'Execucao' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

        return $this->dataManagement->create(entity: $this->modelClassImagem, infos: [
            'id_registro' => $request['id'],
            'nome' => $nome,
            'caminho_imagem' => str_replace("public\\", "", $caminho)
        ]);
    }

    public function update(array $request)
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
    }

    public function destroy(object $registro)
    {
        $registro->load(['imagem']);

        if ($registro->imagem) {
            Storage::delete($registro->imagem['caminho_imagem']);
        }

        return $this->dataManagement->delete(entity: $this->modelClass, id: $registro->id);
    }

    public function deleteImagem($imagem)
    {
        Storage::delete($imagem->caminho_imagem);

        return $this->dataManagement->delete(entity: $this->modelClassImagem, id: $imagem->id);
    }

    public function graficos_monitora(Servicos $servico): array
    {
        $allRegistros = ServicoPassagemFaunaExecRegistro::with(['passagem', 'grupo_faunistico', 'campanha'])
            ->where('id_servico', $servico->id)
            ->get();

        // $especiesGroup = $allRegistros->filter(function ($registro) {
        //     return !empty($registro->especie);
        // })->groupBy('passagem');

        // $sortedEspeciesGroup = $especiesGroup->sortByDesc(function ($grupo) {
        //     return $grupo->count();
        // });


        return [
            // 'especiesGroup' => $sortedEspeciesGroup,
            'chartDataPieAbundancia'  => $this->getChartDataPieAbundancia($allRegistros),
            'chartDataPieDiversidade' => $this->getChartDataPieDiversidade($allRegistros),
            'chartDataBar'            => $this->getChartDataBar($allRegistros),
            'chartDataBar2'           => $this->getChartDataBar2($allRegistros),
            // 'chartDataLine'           => $this->getChartDataLine($allRegistros),
            // 'modulos' => ServicoMonitoraFaunaConfigModuloAmostral::with('armadilhas')->where('id_servico', $servico->id)->get(['id', 'tamanho_modulo']),
        ];
    }

    private function getChartDataPieAbundancia($allRegistros): array
    {
        $abundancia = $allRegistros->groupBy(function ($registro) {
            return $registro->grupo_faunistico
                ? $registro->grupo_faunistico->grupo_faunistico
                : 'Sem Grupo';
        })->map(function ($grupoRegistros, $grupoNome) {
            return [
                'grupo_faunistico' => $grupoNome,
                'total' => $grupoRegistros->count(),
            ];
        })->values();

        return [
            'labels' => $abundancia->pluck('grupo_faunistico')->toArray(),
            'datasets' => [
                [
                    'data' => $abundancia->pluck('total')->toArray(),
                    'backgroundColor' => ["#a6c48a", "#7d9c6d", "#b3c99c", "#d5dfb3"],
                    'borderColor' => "#ffffff",
                    'borderWidth' => 2,
                ],
            ],
        ];
    }

    private function getChartDataPieDiversidade($allRegistros): array
    {
        $diversidade = $allRegistros->groupBy(function ($registro) {
            return $registro->grupo_faunistico
                ? $registro->grupo_faunistico->grupo_faunistico
                : 'Sem Grupo';
        })->map(function ($grupoRegistros, $grupoNome) {
            $uniqueSpecies = $grupoRegistros->pluck('especie')->unique();
            return [
                'grupo_faunistico' => $grupoNome,
                'total' => $uniqueSpecies->count(),
            ];
        })->values();

        return [
            'labels' => $diversidade->pluck('grupo_faunistico')->toArray(),
            'datasets' => [
                [
                    'data' => $diversidade->pluck('total')->toArray(),
                    'backgroundColor' => ["#a6c48a", "#7d9c6d", "#b3c99c", "#d5dfb3"],
                    'borderColor' => "#ffffff",
                    'borderWidth' => 2,
                ],
            ],
        ];
    }

    private function getChartDataBar($allRegistros): array
    {
        $groupPassagem = $allRegistros->groupBy(function ($registro) {
            return $registro->passagem->nome_id;
        });

        return [
            'labels' => $groupPassagem->keys()->map(function ($passagem) {
                return $passagem ? $passagem : 'Sem Passagem';
            })->toArray(),
            'datasets' => [
                [
                    'label' => 'Ocorrências',
                    'data' => $groupPassagem->map(function ($grupo) {
                        return $grupo->count();
                    })->values()->toArray(),
                    'backgroundColor' => "#007bff",
                    'borderRadius' => 5,
                ],
            ],
        ];
    }

    private function getChartDataBar2($allRegistros): array
    {
        $groupCampanha = $allRegistros->groupBy(function ($registro) {
            return $registro->campanha->id;
        });

       
        return [
            'labels' => $groupCampanha->keys()->map(function ($campanha) {
                return $campanha ? $campanha : 'Sem Campanha';
            })->toArray(),
            'datasets' => [
                [
                    'label' => 'Ocorrências',
                    'data' => $groupCampanha->map(function ($grupo) {
                        return $grupo->count();
                    })->values()->toArray(),
                    'backgroundColor' => "#007bff",
                    'borderRadius' => 5,
                ],
            ],
        ];
    }
}
