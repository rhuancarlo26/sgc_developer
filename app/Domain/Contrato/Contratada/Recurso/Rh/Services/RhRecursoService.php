<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Services;

use App\Models\RecursoRh;
use App\Models\RecursoRhDocumento;
use App\Models\RecursoRhDocumentoBaixa;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class RhRecursoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = RecursoRh::class;
  protected string $modelClassDocumento = RecursoRhDocumento::class;
  protected string $modelClassDocumentoBaixa = RecursoRhDocumentoBaixa::class;

  public function listagemRh($contrato, $searchParams)
  {
    return [
      'rhs' => $this->search(...$searchParams)
        ->with(['documentos'])
        ->where('contrato_id', $contrato->id)
        ->paginate()
        ->appends($searchParams)
    ];
  }

  public function salvarRh($request)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return [
      'rh' => $response['model']['id'],
      'request' => $response['request']
    ];
  }

  public function updateRh($request)
  {
    $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

    return [
      'request' => $response['request']
    ];
  }

  public function salvarDocumentoRh($request)
  {
    try {
      foreach ($request['documentos'] as $key => $value) {
        if ($value->isvalid()) {
          $nome = $value->getClientOriginalName();
          $tipo = $value->extension();
          $caminho = $value->storeAs('Contrato' . DIRECTORY_SEPARATOR . 'Recurso' . DIRECTORY_SEPARATOR . 'Rh' . DIRECTORY_SEPARATOR . uniqid() . '_' . $key . '_' . $nome);

          $this->modelClassDocumento::create([
            'recurso_rh_id' => $request['recurso_rh_id'],
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

  public function destroyRh($rh)
  {
    try {
      $documentos = $this->modelClassDocumento::Where('recurso_rh_id', $rh->id)->get();

      foreach ($documentos as $value) {
        Storage::delete($value->caminho);
      }
      return ['type' => 'success', 'content' => 'Documentos excluídos com sucesso!'];
    } catch (\Exception $th) {
      return ['type' => 'error', 'content' => $th->getMessage()];
    }
  }

  public function salvarDocumentoBaixaRh($request)
  {
    try {
      foreach ($request['documentos_baixa'] as $key => $value) {
        if ($value->isvalid()) {
          $nome = $value->getClientOriginalName();
          $tipo = $value->extension();
          $caminho = $value->storeAs('Contrato' . DIRECTORY_SEPARATOR . 'Recurso' . DIRECTORY_SEPARATOR . 'Rh' . DIRECTORY_SEPARATOR . uniqid() . '_' . $key . '_' . $nome);

          $this->modelClassDocumentoBaixa::create([
            'recurso_rh_id' => $request['recurso_rh_id'],
            'nome' => $nome,
            'tipo' => $tipo,
            'caminho' => $caminho
          ]);
        }
      }

      return ['type' => 'success', 'content' => 'Documentos baixa cadastrados com sucesso!'];
    } catch (\Exception $th) {
      return ['type' => 'error', 'content' => $th->getMessage()];
    }
  }
}
