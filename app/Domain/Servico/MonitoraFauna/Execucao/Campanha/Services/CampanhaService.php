<?php

namespace App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Services;

use App\Models\ServicoMonitoraFaunaConfigAbio;
use App\Models\ServicoMonitoraFaunaConfigModuloAmostral;
use App\Models\ServicoMonitoraFaunaExecCampanha;
use App\Models\ServicoMonitoraFaunaExecCampanhaAbio;
use App\Models\ServicoMonitoraFaunaExecCampanhaProfissGrupo;
use App\Models\ServicoMonitoraFaunaExecGrupoFaunistico;
use App\Models\ServicoRh;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Searchable;

class CampanhaService extends BaseModelService
{
  use Searchable;

  protected string $modelClass = ServicoMonitoraFaunaExecCampanha::class;
  protected string $modelClassAbio = ServicoMonitoraFaunaExecCampanhaAbio::class;
  protected string $modelClassProfissional = ServicoMonitoraFaunaExecCampanhaProfissGrupo::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    return [
      'campanhas' => $this->searchAllColumns(...$searchParams)
        ->with([
          'campanha_abios.abio.licenca',
          'profiss_grupo.grupo_faunistico'
        ])
        ->where('id_servico', $servico->id)
        ->paginate()
        ->appends($searchParams)
    ];
  }

  public function create($servico, $campanha)
  {
    return [
      'campanha' => $campanha->load([
        'campanha_abios.abio.licenca',
        'profiss_grupo.rh.rh',
        'profiss_grupo.grupo_faunistico'
      ]),
      'modulos' => ServicoMonitoraFaunaConfigModuloAmostral::where('id_servico', $servico->id)->get(['id']),
      'abios' => ServicoMonitoraFaunaConfigAbio::with(['licenca:id,numero_licenca'])->where('id_servico', $servico->id)->get(['id', 'id_licenca']),
      'rhs' => ServicoRh::with(['rh'])->where('id_servico', $servico->id)->get(),
      'grupo_faunisticos' => ServicoMonitoraFaunaExecGrupoFaunistico::all()
    ];
  }

  public function store(array $post)
  {
    return $this->dataManagement->create($this->modelClass, $post);
  }

  public function delete(object $campanha)
  {
    return $this->dataManagement->delete($this->modelClass, $campanha->id);
  }

  public function store_abio(array $post)
  {
    return $this->dataManagement->create($this->modelClassAbio, $post);
  }

  public function delete_abio(object $campanha_abio)
  {
    return $this->dataManagement->delete($this->modelClassAbio, $campanha_abio->id);
  }

  public function store_profissional(array $post)
  {
    return $this->dataManagement->create($this->modelClassProfissional, $post);
  }

  public function delete_profissional(object $campanha_profissional)
  {
    return $this->dataManagement->delete($this->modelClassProfissional, $campanha_profissional->id);
  }
}
