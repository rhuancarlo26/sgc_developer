<?php

namespace App\Domain\Licenca\Documento\Services;

use App\Models\LicencaDocumento;
use App\Models\LicencaRequerimento;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class LicencaDocumentoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = LicencaDocumento::class;

  public function store($documento, $licenca_id)
  {
    try {
      if ($documento->isvalid()) {
        $nome = $documento->getClientOriginalName();
        $tipo = $documento->extension();
        $caminho = $documento->storeAs('Licenca' . DIRECTORY_SEPARATOR . 'Documento' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

        $this->modelClass::create([
          'licenca_id' => $licenca_id,
          'nome' => $nome,
          'tipo' => $tipo,
          'caminho' => $caminho
        ]);
      }

      return ['type' => 'success', 'content' => 'Documento cadastrados com sucesso!'];
    } catch (\Exception $th) {
      return ['type' => 'error', 'content' => $th->getMessage()];
    }
  }
}