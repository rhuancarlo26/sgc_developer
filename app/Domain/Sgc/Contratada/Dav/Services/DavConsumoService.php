<?php

namespace App\Domain\Sgc\Contratada\Dav\Services;

use App\Models\SgcConsumoDav;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class DavConsumoService
{
  use Searchable, Deletable;

  protected string $modelClass = SgcConsumoDav::class;

  public function updateConsumo(array $dados) // Forçar que $dados seja array
  {
    if (!isset($dados['contrato'])) {
      throw new \InvalidArgumentException('O número do contrato é obrigatório.');
    }

    $contrato = $dados['contrato'];
    unset($dados['contrato']);

    $registro = $this->modelClass::where('contrato', $contrato)->first();

    if (!$registro) {
      throw new \Exception('Registro não encontrado para o contrato: ' . $contrato);
    }

    $registro->fill($dados);
    $registro->save();

    return $registro;
  }
}
