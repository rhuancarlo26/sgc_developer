<?php

namespace App\Domain\Sgc\Contratada\Dav\Services;

use App\Models\SgcConsumoDav;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class DavConsumoService
{
  use Searchable, Deletable;

  protected string $modelClass = SgcConsumoDav::class;

  public function updateConsumo($dados)
  {
    // Verifica se o ID do registro a ser atualizado está presente nos dados
    if (!isset($dados['contrato'])) {
      throw new \InvalidArgumentException('O ID do registro a ser atualizado é obrigatório.');
    }

    // Obtém o ID do registro
    $contrato = $dados['contrato'];

    // Remove o ID dos dados para evitar tentativas de atualização do próprio ID
    unset($dados['contrato']);

    // Encontra o registro pelo ID
    $registro = $this->modelClass::find($contrato);

    if (!$registro) {
      throw new \Exception('Registro não encontrado.');
    }

    // Atualiza os dados do registro
    $registro->fill($dados);
    $registro->save();

    return $registro;
  }
}
