<?php

namespace App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Services;

use App\Models\Licenca;
use App\Models\ServicoMonAtpFaunaVincularABIO;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class VincularABIOService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoMonAtpFaunaVincularABIO::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    return [
      'vinculacoes' => $this->searchAllColumns(...$searchParams)
        ->with(['licenca.tipo', 'rets' => fn($query) => $query->orderBy('id', 'desc')])
        ->where('fk_servico', $servico->id)
        ->paginate()
        ->appends($searchParams),
      'licencas' => Licenca::get(['id', 'numero_licenca'])
    ];
  }

  public function store(array $post)
  {
    return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
  }
}
