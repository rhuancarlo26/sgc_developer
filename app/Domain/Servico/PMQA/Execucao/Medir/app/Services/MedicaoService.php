<?php

namespace App\Domain\Servico\PMQA\Execucao\Medir\app\Services;

use App\Models\ServicoPmqaCampanhaPontoMedicao;
use App\Models\ServicoPmqaCampanhaPontoMedicaoArquivo;
use App\Models\ServicoPmqaCampanhaPontoMedicaoParametro;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class MedicaoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoPmqaCampanhaPontoMedicao::class;
  protected string $modelClassArquivo = ServicoPmqaCampanhaPontoMedicaoArquivo::class;

  public function store(array $request): array
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    if ($request['parametros']) {
      $this->syncParametros($response['model'], $request['parametros']);
    }

    return $response;
  }

  public function update(array $request): array
  {
    $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

    if ($request['parametros']) {
      $this->syncParametros(ServicoPmqaCampanhaPontoMedicao::find($request['id']), $request['parametros']);
    }

    return $response;
  }

  public function syncParametros(ServicoPmqaCampanhaPontoMedicao $medicao, array $parametros): void
  {
    ServicoPmqaCampanhaPontoMedicaoParametro::where('ponto_medicao_id', $medicao->id)->delete();
    foreach ($parametros as $key => $value) {

      ServicoPmqaCampanhaPontoMedicaoParametro::create([
        'ponto_medicao_id' => $medicao->id,
        'lista_parametro_id' => $key,
        'medicao' => $value
      ]);
    }
  }

  public function storeArquivo(array $request): array
  {
    if ($request['arquivo']->isvalid()) {
      $nome = $request['arquivo']->getClientOriginalName();
      $tipo = $request['arquivo']->extension();
      $caminho = $request['arquivo']->storeAs('Servico' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Medicao' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

      return $this->dataManagement->create(entity: $this->modelClassArquivo, infos: [
        'medicao_id' => $request['id'],
        'nome' => $nome,
        'tipo' => $tipo,
        'caminho' => $caminho
      ]);
    }
  }

  public function deleteArquivo(ServicoPmqaCampanhaPontoMedicaoArquivo $arquivo): array
  {
    Storage::delete($arquivo->caminho);

    return $this->dataManagement->delete(entity: $this->modelClassArquivo, id: $arquivo->id);
  }
}