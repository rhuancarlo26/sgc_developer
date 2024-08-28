<?php

namespace App\Domain\Servico\ControleDeOcorrencias\Configuracoes\Empreendimento\Services;

use App\Models\ServicoLicencaCondicionante;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class EmpreendimentoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoLicencaCondicionante::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    return [
      'empreendimentos' => $this->searchAllColumns(...$searchParams)
      ->with(['licenca.segmentos', 'condicionante'])
        ->where('servico_id', '=', $servico->id)
        ->paginate()
        ->appends($searchParams),
    ];
  }
}
