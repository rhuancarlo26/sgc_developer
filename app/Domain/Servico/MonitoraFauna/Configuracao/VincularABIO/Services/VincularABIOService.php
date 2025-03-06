<?php

namespace App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Services;

use App\Models\Licenca;
use App\Models\ServicoMonitoraFaunaConfigAbio;
use App\Models\ServicoMonitoraFaunaConfigAbioRet;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Searchable;

class VincularABIOService extends BaseModelService
{
  use Searchable;

  protected string $modelClass = ServicoMonitoraFaunaConfigAbio::class;
  protected string $modelClassRet = ServicoMonitoraFaunaConfigAbioRet::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    return [
      'vinculacoes' => $this->searchAllColumns(...$searchParams)
        ->with(['licenca.tipo_rel', 'rets' => fn($query) => $query->orderBy('id', 'desc')])
        ->where('id_servico', $servico->id)
        ->paginate()
        ->appends($searchParams),
      'licencas' => Licenca::with(['tipo_rel'])->get(['id', 'numero_licenca', 'tipo'])
    ];
  }

  public function store(array $post)
  {
    return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
  }

  public function store_ret(array $post)
  {
    $nome = $post['arquivo']->getClientOriginalName();
    $caminho = $post['arquivo']->storeAs('Servico' . DIRECTORY_SEPARATOR . 'MonitoraFauna' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

    return $this->dataManagement->create(entity: $this->modelClassRet, infos: [
      'id_abio' => $post['id_abio'],
      'caminho_ret' => $caminho,
      'nome' => $nome
    ]);
  }

  public function destroy(object $abio)
  {
    return $this->dataManagement->delete(entity: $this->modelClass, id: $abio->id);
  }

  public function destroy_ret(object $abio_ret)
  {
    return $this->dataManagement->delete(entity: $this->modelClassRet, id: $abio_ret->id);
  }
}
