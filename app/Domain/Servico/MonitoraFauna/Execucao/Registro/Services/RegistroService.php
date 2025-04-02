<?php

namespace App\Domain\Servico\MonitoraFauna\Execucao\Registro\Services;

use App\Models\ServicoMonitoraFaunaConfigModuloAmostral;
use App\Models\ServicoMonitoraFaunaExecCampanha;
use App\Models\ServicoMonitoraFaunaExecGrupoFaunistico;
use App\Models\ServicoMonitoraFaunaExecRegistro;
use App\Models\ServicoMonitoraFaunaExecStatusConservacao;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Searchable;
use Carbon\Carbon;

class RegistroService extends BaseModelService
{
  use Searchable;

  protected string $modelClass = ServicoMonitoraFaunaExecRegistro::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    return [
      'registros' => $this->searchAllColumns(...$searchParams)
        ->with(['armadilha', 'grupo_faunistico'])
        ->where('id_servico', $servico->id)
        ->paginate()
        ->appends($searchParams)
    ];
  }


  public function graficos_monitora(Servicos $servico): array
  {
    $allRegistros = ServicoMonitoraFaunaExecRegistro::with(['grupo_faunistico', 'modulo', 'armadilha'])
      ->where('id_servico', $servico->id)
      ->get();

    $especiesGroup = $allRegistros->filter(function ($registro) {
      return !empty($registro->especie);
    })->groupBy('especie');

    $sortedEspeciesGroup = $especiesGroup->sortByDesc(function ($grupo) {
      return $grupo->count();
    });


    return [
      'especiesGroup' => $sortedEspeciesGroup,
      'chartDataPieAbundancia'  => $this->getChartDataPieAbundancia($allRegistros),
      'chartDataPieDiversidade' => $this->getChartDataPieDiversidade($allRegistros),
      'chartDataBar'            => $this->getChartDataBar($allRegistros),
      'chartDataLine'           => $this->getChartDataLine($allRegistros),
      'chartDataBar2'           => $this->getChartDataBar2($especiesGroup),
      'modulos' => ServicoMonitoraFaunaConfigModuloAmostral::with('armadilhas')->where('id_servico', $servico->id)->get(['id', 'tamanho_modulo']),
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
    $groupModulo = $allRegistros->groupBy(function ($registro) {
      return $registro->id_modulo;
    });


    return [
      'labels' => $groupModulo->keys()->map(function ($idModulo) {
        return $idModulo ? $idModulo : 'Sem Módulo';
      })->toArray(),
      'datasets' => [
        [
          'label' => 'Ocorrências',
          'data' => $groupModulo->map(function ($grupo) {
            return $grupo->count();
          })->values()->toArray(),
          'backgroundColor' => "#007bff",
          'borderRadius' => 5,
        ],
      ],
    ];
  }

  private function getChartDataLine($allRegistros): array
  {

    $registrosPorData = $allRegistros->filter(function ($registro) {
      return !empty($registro->data_registro);
    })->groupBy('data_registro');


    $sortedDates = $registrosPorData->keys()->sort();

    $labels = [];
    $cumulativeData = [];
    $cumulativeSum = 0;

    $meses = [
      '01' => 'Jan',
      '02' => 'Fev',
      '03' => 'Mar',
      '04' => 'Abr',
      '05' => 'Mai',
      '06' => 'Jun',
      '07' => 'Jul',
      '08' => 'Ago',
      '09' => 'Set',
      '10' => 'Out',
      '11' => 'Nov',
      '12' => 'Dez'
    ];

    foreach ($sortedDates as $date) {
      $count = $registrosPorData->get($date)->count();
      $cumulativeSum += $count;


      $dt = Carbon::parse($date);
      $day = $dt->format('d');
      $month = $meses[$dt->format('m')];
      $labels[] = "$day de $month";
      $cumulativeData[] = $cumulativeSum;
    }

    return [
      'labels' => $labels,
      'datasets' => [
        [
          'label' => 'Ocorrências',
          'data' => $cumulativeData,
          'borderColor' => "#007bff",
          'backgroundColor' => "rgba(0, 123, 255, 0.3)",
          'fill' => true,
          'pointBackgroundColor' => "#007bff",
          'pointBorderColor' => "#ffffff",
          'pointRadius' => 5,
        ],
      ],
    ];
  }

  private function getChartDataBar2($especiesGroup): array
  {
    // Ordena os grupos de acordo com a contagem de ocorrências (do maior para o menor)
    $sortedGroup = $especiesGroup->sortByDesc(function ($grupo) {
      return $grupo->count();
    });

    return [
      'labels' => $sortedGroup->keys()->toArray(),
      'datasets' => [
        [
          'label' => 'Ocorrências',
          'data' => $sortedGroup->map(function ($grupo) {
            return $grupo->count();
          })->values()->toArray(),
          'backgroundColor' => "rgba(30, 144, 255, 0.8)",
          'borderColor' => "rgba(30, 144, 255, 1)",
          'borderWidth' => 1,
        ],
      ],
    ];
  }


  public function create(object $servico, object $registro)
  {
    return [
      'registro' => $registro,
      'campanhas' => ServicoMonitoraFaunaExecCampanha::where('id_servico', $servico->id)->get(['id']),
      'modulos' => ServicoMonitoraFaunaConfigModuloAmostral::with('armadilhas')->where('id_servico', $servico->id)->get(['id', 'tamanho_modulo']),
      'grupo_faunisticos' => ServicoMonitoraFaunaExecGrupoFaunistico::all(),
      'status_conservacao' => ServicoMonitoraFaunaExecStatusConservacao::all()
    ];
  }

  public function store(array $post)
  {
    return $this->dataManagement->create($this->modelClass, $post);
  }

  public function update(array $post)
  {
    return $this->dataManagement->update($this->modelClass, $post, $post['id']);
  }

  public function delete(object $registro)
  {
    return $this->dataManagement->delete($this->modelClass, $registro->id);
  }
}
