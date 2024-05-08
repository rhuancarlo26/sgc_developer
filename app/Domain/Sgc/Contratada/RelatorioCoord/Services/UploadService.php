<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Services;

use App\Models\RelatorioUpload;
use App\Models\SgcRelatorioCoordenacao;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class UploadService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = RelatorioUpload::class;

  public function obterItensSgcRelatorioCoordenacao()
  {
      return SgcRelatorioCoordenacao::all();
  }
  
  public function salvarAnexo($request)
  {
    $arquivo = $this->storageAnexo($request['arquivo']);

    $form = [
      ...$arquivo,
      'contrato_id' => $request['contrato_id'],
      'descricao' => $request['descricao']
    ];

    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $form);

    return [
      'request' => $response['request']
    ];
  }

  public function storageAnexo($arquivo)
  {
    $nome = $arquivo->getClientOriginalName();
    $tipo = $arquivo->extension();
    $caminho = $arquivo->storeAs('Relatorio' . DIRECTORY_SEPARATOR . 'Sgc' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

    return [
      'nome' => $nome,
      'tipo' => $tipo,
      'caminho' => $caminho
    ];
  }
  
}
