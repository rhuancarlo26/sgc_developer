<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Services;

use App\Models\SgcRelatorioUpload;
use App\Models\SgcRelatorioCoordenacao;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\DB;

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
      return DB::transaction(function () use ($request) {
          // Serializa uploads e mudancas de revisao para este relatorio.
          SgcRelatorioCoordenacao::where('contrato_id', $request['contrato_id'])
              ->where('relatorio_num', $request['relatorio_num'])->lockForUpdate()->get();

          $versao = AnexosRelatorioService::versaoAtual($request['contrato_id'], $request['relatorio_num']);
          $existente = AnexosRelatorioService::consulta($request['contrato_id'], $request['relatorio_num'], $versao)
              ->where('item_id', $request['item_id'])->first();
          $arquivo = $this->storageAnexo($request['arquivo']);

          if ($existente) {
              $existente->update($arquivo);
          } else {
              $existente = SgcRelatorioUpload::create([
                  ...$arquivo,
                  'contrato_id' => $request['contrato_id'],
                  'item_id' => $request['item_id'],
                  'num_relatorio' => $request['relatorio_num'],
                  'versao' => $versao,
              ]);
          }

          return ['request' => $existente];
      });
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
      $arquivo = AnexosRelatorioService::consulta($contratoId, $relatorioNum)
          ->where('item_id', $idItem)->first();

      return $arquivo ? $arquivo->caminho : null;
  }
  
}
