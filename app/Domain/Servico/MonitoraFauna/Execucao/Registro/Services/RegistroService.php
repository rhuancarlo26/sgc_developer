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
