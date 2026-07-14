<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Services;

use App\Models\SgcRelatorioUpload;
use App\Models\SgcRelatorioCoordenacao;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class UploadService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = SgcRelatorioUpload::class;

  public function obterItensSgcRelatorioCoordenacao()
  {
      return SgcRelatorioCoordenacao::all();
  }
  
  public function salvarAnexo($request)
  {
      // Verifica se já existe um anexo com os mesmos parâmetros
      $arquivoExistente = SgcRelatorioUpload::where('item_id', $request['item_id'])
          ->where('contrato_id', $request['contrato_id'])
          ->where('num_relatorio', $request['relatorio_num'])
          ->orderByDesc('versao')
          ->first();
  
      // Salva o novo arquivo no sistema
      $arquivo = $this->storageAnexo($request['arquivo']);
  
      if ($arquivoExistente) {
          // Se já existir, atualiza o registro existente com o novo arquivo
          $arquivoExistente->update([
              'nome' => $arquivo['nome'],
              'tipo' => $arquivo['tipo'],
              'caminho' => $arquivo['caminho']
          ]);
  
          $response = ['request' => $arquivoExistente];
      } else {
          // Se não existir, cria um novo registro
          $form = [
              ...$arquivo,
              'contrato_id' => $request['contrato_id'],
              'item_id' => $request['item_id'],
              'num_relatorio' => $request['relatorio_num'],
              'versao' => 0, 
          ];
  
          $response = $this->dataManagement->create(entity: $this->modelClass, infos: $form);
      }
  
      return [
          'request' => $response['request']
      ];
  }

//   public function storageAnexo($arquivo)
//   {
//     $nome = $arquivo->getClientOriginalName();
//     $tipo = $arquivo->extension();
//     $caminho = $arquivo->storeAs('Relatorio' . DIRECTORY_SEPARATOR . 'Sgc' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

//     return [
//       'nome' => $nome,
//       'tipo' => $tipo,
//       'caminho' => $caminho
//     ];
//   }

  public function storageAnexo($arquivo)
{
    $nome = $arquivo->getClientOriginalName();
    $tipo = $arquivo->extension();
    $caminho = $arquivo->storeAs('public/Relatorio/Sgc', uniqid() . '_' . $nome);

    // Remove o prefixo 'public/' do caminho salvo no banco para facilitar o uso no frontend
    $caminhoRelativo = str_replace('public/', '', $caminho);

    return [
        'nome' => $nome,
        'tipo' => $tipo,
        'caminho' => $caminhoRelativo
    ];
}

  public function obterCaminhoAnexo($idItem, $contratoId, $relatorioNum)
  {
      $arquivo = SgcRelatorioUpload::where('item_id', $idItem)
          ->where('contrato_id', $contratoId)
          ->where('num_relatorio', $relatorioNum)
          ->first();

      return $arquivo ? $arquivo->caminho : null;
  }
  
}
