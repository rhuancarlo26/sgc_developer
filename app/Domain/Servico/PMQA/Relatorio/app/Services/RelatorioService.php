<?php

namespace App\Domain\Servico\PMQA\Relatorio\app\Services;

use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaParametroLista;
use App\Models\ServicoPmqaPonto;
use App\Models\ServicoPmqaRelatorio;
use App\Models\ServicoPmqaResultado;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Contracts\Database\Eloquent\Builder;

class RelatorioService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoPmqaRelatorio::class;

  public function index(Servicos $servico): array
  {
    $resultados = ServicoPmqaResultado::with(['campanhas'])->where('servico_id', $servico->id)->get();

    return [
      'resultados' => $resultados
    ];
  }

  public function store(array $request): array
  {
    $relatorioModel = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return $relatorioModel;
  }
}
