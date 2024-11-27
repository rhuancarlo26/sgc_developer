<?php

namespace App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Services;

use App\Models\ServicoMonitoraFaunaConfigAbio;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Searchable;

class VincularABIOService extends BaseModelService
{
  use Searchable;

  protected string $modelClass = ServicoMonitoraFaunaConfigAbio::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    return [
      'vinculacoes' => $this->searchAllColumns(...$searchParams)
        ->where('id_servico', $servico->id)
        ->paginate()
        ->appends($searchParams)
    ];
  }
}
