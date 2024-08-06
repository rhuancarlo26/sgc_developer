<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services;

use App\Models\Rodovia;
use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoContOcorrSupervisaoConfigLote;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class OcorrenciaService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoConOcorrOcorrenciSupervisaoExecOcorrencia::class;

  public function create(array $post): array
  {
    return [
      'lotes' => ServicoContOcorrSupervisaoConfigLote::where('id_servico', $post['servico_id'])->get(),
      'rodovias' => Rodovia::with(['uf'])->get()
    ];
  }

  public function store(array $post)
  {
    return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
  }
}
