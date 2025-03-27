<?php

namespace App\Domain\Servico\MonitoraFauna\Resultado\Services;

use App\Models\ServicoMonitoraFaunaExecCampanha;
use App\Models\ServicoMonitoraFaunaExecGrupoFaunistico;
use App\Models\ServicoMonitoraFaunaResultado;
use App\Models\ServicoMonitoraFaunaResultadoCampanha;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Searchable;
use Carbon\Carbon;

class ResultadoService extends BaseModelService
{
  use Searchable;

  protected string $modelClass = ServicoMonitoraFaunaResultado::class;
  protected string $modelClassCampanha = ServicoMonitoraFaunaResultadoCampanha::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    return [
      'resultados' => $this->searchAllColumns(...$searchParams)
        ->with(['campanhas', 'campanhas'])
        ->where('id_servico', $servico->id)
        ->paginate()
        ->appends($searchParams),
      'campanhas' => ServicoMonitoraFaunaExecCampanha::select(['id'])->where('id_servico', $servico->id)->get()
    ];
  }

  public function store($post)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $post);

    $response['model']->campanhas()->sync(collect($post['campanhas'])->pluck('id'));

    return $response;
  }

  public function update($post)
  {
    $response = $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);

    $this->modelClass::find($post['id'])->campanhas()->sync(collect($post['campanhas'])->pluck('id'));

    return $response;
  }

  public function destroy($resultado)
  {
    $resultado->campanhas()->sync(collect([]));

    return $this->dataManagement->delete(entity: $this->modelClass, id: $resultado->id);
  }

  public function resultado($resultado)
  {
    $resultado->load([
      'campanhas.registros.grupo_faunistico',
      'campanhas.registros.armadilha'
    ]);

    $registros = $resultado->campanhas->flatMap(function ($campanha) {
      return $campanha->registros;
    });

    $dadosTabela = $registros->groupBy('grupo_faunistico.grupo_faunistico')->map(function ($grupo) {
      return $grupo->groupBy('especie')->map(function ($items, $especie) use ($grupo) {
        $abundanciaAbsoluta = $items->count();
        $abundanciaRelativa = $grupo->count() > 0 ? ($abundanciaAbsoluta / $grupo->count()) * 100 : 0;

        return [
          'especie' => $especie,
          'nome_comum' => $items->first()->nome_comum,
          'modulos' => $items->pluck('id_modulo')->unique()->toArray(),
          'abundancia_absoluta' => $abundanciaAbsoluta,
          'abundancia_relativa' => $abundanciaRelativa,
          'status_conservacao_federal' => $items->first()->status_conserv_federal ?? '-',
          'status_conservacao_iucn' => $items->first()->status_conserv_iucn ?? '-',
        ];
      });
    });

    // CHART CAMPANHA
    $dadosChart = $registros->groupBy('grupo_faunistico.grupo_faunistico')->map(function ($grupo, $grupoNome) {
      $dados = $grupo->groupBy('id_campanha')->map(function ($registros) {
        return $registros->groupBy('especie')->count();
      });

      return [
        'grupo_faunistico' => $grupoNome,
        'dados' => $dados,
      ];
    });

    $dadosCampanha = $dadosChart->mapWithKeys(function ($grupo) {
      $labels = $grupo['dados']->keys();
      $data = $grupo['dados']->map(function ($quantidades) {
        return $quantidades;
      });

      return [
        $grupo['grupo_faunistico'] => [
          'chartData' => [
            'labels' => $labels,
            'datasets' => [
              [
                'label' => 'Quantidade de Espécies',
                'data' => $data,
                'backgroundColor' => '#' . substr(md5($grupo['grupo_faunistico']), 0, 6),
              ],
            ],
          ],
        ],
      ];
    });
    // CHART CAMPANHA
    // CHART METODO
    $dadosChart = $registros->groupBy('grupo_faunistico.grupo_faunistico')->map(function ($grupo, $grupoNome) {
      $dados = $grupo->groupBy('armadilha.tipo')->map(function ($registros) {
        return $registros->groupBy('especie')->count();
      });

      return [
        'grupo_faunistico' => $grupoNome,
        'dados' => $dados,
      ];
    });

    $dadosArmadilha = $dadosChart->mapWithKeys(function ($grupo) {
      $labels = $grupo['dados']->keys();
      $data = $grupo['dados']->map(function ($quantidades) {
        return $quantidades;
      });

      return [
        $grupo['grupo_faunistico'] => [
          'chartData' => [
            'labels' => $labels,
            'datasets' => [
              [
                'label' => 'Quantidade de Espécies',
                'data' => $data,
                'backgroundColor' => '#' . substr(md5($grupo['grupo_faunistico']), 0, 6),
              ],
            ],
          ],
        ],
      ];
    });
    // CHART METODO
    // CHART ORDEM
    $dadosChart = $registros->groupBy('grupo_faunistico.grupo_faunistico')->map(function ($grupo, $grupoNome) {
      $dados = $grupo->groupBy('especie')->map(function ($registros) {
        return $registros->count();
      });

      return [
        'grupo_faunistico' => $grupoNome,
        'dados' => $dados,
      ];
    });

    $dadosOrdem = $dadosChart->mapWithKeys(function ($grupo) {
      $labels = $grupo['dados']->keys();
      $data = array_values($grupo['dados']->toArray());

      return [
        $grupo['grupo_faunistico'] => [
          'chartData' => [
            'labels' => $labels,
            'datasets' => [
              [
                'label' => 'Quantidade de Espécies',
                'data' => $data,
                'backgroundColor' => [
                  '#FF6384',
                  '#36A2EB',
                  '#FFCE56',
                  '#FF5733',
                  '#C70039'
                ],
              ],
            ],
          ],
        ],
      ];
    });
    // CHART ORDEM
    // CHART FAMILIA
    $dadosChart = $registros->groupBy('grupo_faunistico.grupo_faunistico')->map(function ($grupo, $grupoNome) {
      $dados = $grupo->groupBy('familia')->map(function ($registros) {
        return $registros->groupBy('especie')->count();
      });

      return [
        'grupo_faunistico' => $grupoNome,
        'dados' => $dados,
      ];
    });

    $dadosFamilia = $dadosChart->mapWithKeys(function ($grupo) {
      $labels = $grupo['dados']->keys();
      $data = array_values($grupo['dados']->toArray());

      return [
        $grupo['grupo_faunistico'] => [
          'chartData' => [
            'labels' => $labels,
            'datasets' => [
              [
                'label' => 'Quantidade de Espécies',
                'data' => $data,
                'backgroundColor' => [
                  '#FF6384',
                  '#36A2EB',
                  '#FFCE56',
                  '#FF5733',
                  '#C70039'
                ],
              ],
            ],
          ],
        ],
      ];
    });
    // CHART FAMILIA
    // CHART COLETOR
    $dadosChart = $registros->groupBy('grupo_faunistico.grupo_faunistico')->map(function ($grupo, $grupoNome) {
      $dados = $grupo->groupBy(function ($item) {
        return Carbon::parse($item->data_registro)->format('d/m/Y');
      })->map(function ($registros) {
        return $registros->groupBy('especie')->count();
      })->sortKeys();

      return [
        'grupo_faunistico' => $grupoNome,
        'dados' => $dados,
      ];
    });

    $dadosColetor = $dadosChart->mapWithKeys(function ($grupo) {
      $labels = $grupo['dados']->keys()->toArray();
      $data = $grupo['dados']->values()->map(function ($item) {
        return $item;
      })->toArray();

      return [
        $grupo['grupo_faunistico'] => [
          'chartData' => [
            'labels' => $labels,
            'datasets' => [
              [
                'label' => 'Quantidade de Espécies',
                'data' => $data,
                'backgroundColor' => [
                  '#FF6384',
                  '#36A2EB',
                  '#FFCE56',
                  '#FF5733',
                  '#C70039',
                ],
              ],
            ],
          ],
        ],
      ];
    });
    // CHART COLETOR

    return [
      'grupo_faunisticos' => ServicoMonitoraFaunaExecGrupoFaunistico::all(),
      'dados_tabela' => $dadosTabela,
      'dados_campanha' => $dadosCampanha,
      'dados_armadilha' => $dadosArmadilha,
      'dados_ordem' => $dadosOrdem,
      'dados_familia' => $dadosFamilia,
      'dados_coletor' => $dadosColetor
    ];
  }
}
