<?php

namespace App\Domain\Licenca\Requerimento\Services;

use App\Models\LicencaRequerimento;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class RequerimentoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = LicencaRequerimento::class;

  public function store($arquivos, $licenca_id)
  {
    try {
      foreach ($arquivos as $key => $value) {
        if ($value->isvalid()) {
          $nome = $value->getClientOriginalName();
          $tipo = $value->extension();
          $caminho = $value->storeAs('Licenca' . DIRECTORY_SEPARATOR . 'Requerimento' . DIRECTORY_SEPARATOR . uniqid() . '_' . $key . '_' . $nome);

          $this->modelClass::create([
            'licenca_id' => $licenca_id,
            'nome' => $nome,
            'tipo' => $tipo,
            'caminho' => $caminho
          ]);
        }
      }

      return ['type' => 'success', 'content' => 'Requerimentos cadastrados com sucesso!'];
    } catch (\Exception $th) {
      return ['type' => 'error', 'content' => $th->getMessage()];
    }
  }
}
