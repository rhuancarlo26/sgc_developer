<?php

namespace App\Domain\Servico\MonitoraFauna\Execucao\Registro\Services;

use App\Models\ServicoMonitoraFaunaExecRegistro;
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
        ->where('id_servico', $servico->id)
        ->paginate()
        ->appends($searchParams)
    ];
  }

  public function create(object $registro)
  {
    return [
      'registro' => $registro
    ];
  }
}
