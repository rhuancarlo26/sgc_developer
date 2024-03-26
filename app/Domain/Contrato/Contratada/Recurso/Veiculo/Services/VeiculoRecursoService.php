<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Services;

use App\Models\RecursoVeiculo;
use App\Models\RecursoVeiculoCodigo;
use App\Models\RecursoVeiculoDocumento;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class VeiculoRecursoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = RecursoVeiculo::class;
  protected string $modelClassDocumento = RecursoVeiculoDocumento::class;

  public function listagemVeiculos($searchParams)
  {
    return [
      'veiculos' => $this->search(...$searchParams)
        ->with(['codigo', 'documentos'])
        ->paginate()
        ->appends($searchParams)
    ];
  }

  public function createForm()
  {
    return [
      'codigos' => RecursoVeiculoCodigo::all()
    ];
  }

  public function salvarVeiculo($request)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return [
      'veiculo' => $response['model'],
      'request' => $response['request']
    ];
  }

  public function salvarDocumentoVeiculo($request)
  {
    try {
      foreach ($request['documentos'] as $key => $value) {
        if ($value->isvalid()) {
          $nome = $value->getClientOriginalName();
          $tipo = $value->extension();
          $caminho = $value->storeAs('Contrato' . DIRECTORY_SEPARATOR . 'Recurso' . DIRECTORY_SEPARATOR . 'Veiculo' . DIRECTORY_SEPARATOR . uniqid() . '_' . $key . '_' . $nome);

          $this->modelClassDocumento::create([
            'recurso_veiculo_id' => $request['recurso_veiculo_id'],
            'nome' => $nome,
            'tipo' => $tipo,
            'caminho' => $caminho
          ]);
        }
      }

      return ['type' => 'success', 'content' => 'Documentos cadastrados com sucesso!'];
    } catch (\Exception $th) {
      return ['type' => 'error', 'content' => $th->getMessage()];
    }
  }

  public function destroyVeiculo($veiculo)
  {
    try {
      $documentos = $this->modelClassDocumento::Where('recurso_veiculo_id', $veiculo->id)->get();

      foreach ($documentos as $value) {
        Storage::delete($value->caminho);
      }
      return ['type' => 'success', 'content' => 'Documentos excluídos com sucesso!'];
    } catch (\Exception $th) {
      return ['type' => 'error', 'content' => $th->getMessage()];
    }
  }
}
