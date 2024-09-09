<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Imports\PMQAPontoImport;
use App\Models\Licenca;
use App\Models\ServicoMonAtpFaunaVincularABIO;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CampanhasService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoMonAtpFaunaVincularABIO::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    return [
      'vinculacoes' => $this->searchAllColumns(...$searchParams)
        ->with(['licenca.tipo'])
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
