<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services;

use App\Models\Rodovia;
use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervicaoExecOcorrenciaRegistro;
use App\Models\ServicoContOcorrSupervisaoConfigLote;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class OcorrenciaService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoConOcorrOcorrenciSupervisaoExecOcorrencia::class;
  protected string $modelClassRegistro = ServicoConOcorrSupervicaoExecOcorrenciaRegistro::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    return [
      'ocorrencias' => $this->searchAllColumns(...$searchParams)
        ->with(['lote'])
        ->where('id_servico', $servico->id)
        ->paginate()
        ->appends($searchParams)
    ];
  }

  public function create(array $post): array
  {
    return [
      'lotes' => ServicoContOcorrSupervisaoConfigLote::where('id_servico', $post['servico_id'])->get(),
      'rodovias' => Rodovia::with(['uf'])->get()
    ];
  }

  public function store(array $post): array
  {
    return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
  }

  public function storeRegistro(array $post): array
  {
    $nome = $post['arquivo']->getClientOriginalName();
    $caminho = $post['arquivo']->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Registro' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

    return $this->dataManagement->create(entity: $this->modelClassRegistro, infos: [
      'id_ocorrencia' => $post['id_ocorrencia'],
      'nome' => $nome,
      'caminho_arquivo' => $caminho
    ]);
  }

  public function update(array $post): array
  {
    return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
  }

  public function destroy(array $post): array
  {
    return $this->dataManagement->delete(entity: $this->modelClass, id: $post['id']);
  }

  public function destroyRegistro($registro): array
  {
    Storage::delete($registro->caminho_arquivo);

    return $this->dataManagement->delete(entity: $this->modelClassRegistro, id: $registro->id);
  }
}
