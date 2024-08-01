<?php

namespace App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services;

use App\Models\ServicoLicencaCondicionante;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class LoteObraService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoLicencaCondicionante::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    return [
      'lotes' => $this->searchAllColumns(...$searchParams)
        ->where('servico_id', $servico->id)
        ->paginate()
        ->appends($searchParams)
    ];
  }
}