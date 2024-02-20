<?php

namespace App\Domain\Licenca\Condicionante\Services;

use App\Models\Licenca;
use App\Models\LicencaCondicionante;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class CondicionanteService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = LicencaCondicionante::class;

  public function listarCondicionantes($licenca, $searchParams)
  {
    return $this->search(...$searchParams)
      ->where('licenca_id', $licenca['id'])
      ->paginate()
      ->appends($searchParams);
  }

  public function store($request)
  {
    try {
      $this->modelClass::create($request);

      return ['type' => 'success', 'content' => 'Condicionante cadastrado com sucesso!'];
    } catch (\Exception $th) {
      return ['type' => 'error', 'content' => $th->getMessage()];
    }
  }

  public function update($licenca_id, $request)
  {
    try {
      LicencaCondicionante::find($licenca_id)->update($request);

      return ['type' => 'success', 'content' => 'Condicionante alterado com sucesso!'];
    } catch (\Exception $th) {
      return ['type' => 'error', 'content' => $th->getMessage()];
    }
  }

  public function buscarLicenca($request)
  {
    try {
      $licenca = Licenca::with(['condicionantes'])->where('numero_licenca', $request['numero_licenca'])->firstOrFail();
    } catch (\Exception $th) {
    }

    return $licenca->condicionantes;
  }

  public function storeImportacao($request)
  {
    try {
      foreach ($request['condicionantes'] as $value) {
        $value['licenca_id'] = $request['licenca']['id'];

        $this->modelClass::create($value);
      }
    } catch (\Throwable $th) {
      //throw $th;
    }
  }
}