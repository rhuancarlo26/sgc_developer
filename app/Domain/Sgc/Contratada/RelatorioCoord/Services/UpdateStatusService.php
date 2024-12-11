<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Services;

use App\Models\SgcRelatorioCoordenacao;
use App\Models\SgcHistoricoRelatorio;
use App\Models\SgcRelatorioUpload;
use Illuminate\Support\Facades\DB;

class UpdateStatusService
{
    public function updateStatus($contrato_id, $relatorio_num)
    {
        SgcRelatorioCoordenacao::where('contrato_id', $contrato_id)
            ->where('relatorio_num', $relatorio_num)
            ->update(['status' => 'Análise DNIT']);
    }

    public function SGCrevisaoStatus($contrato_id, $relatorio_num)
    {
        SgcRelatorioCoordenacao::where('contrato_id', $contrato_id)
            ->where('relatorio_num', $relatorio_num) 
            ->update(['status' => 'Revisão Contratada']);
    }

    public function revisaoStatus($contrato_id, $relatorio_num)
    {
        DB::transaction(function() use ($contrato_id, $relatorio_num) {
            
            $relatorios = SgcRelatorioCoordenacao::where('contrato_id', $contrato_id)
                ->where('relatorio_num', $relatorio_num)
                ->get();

            $ultimaVersaoHistorico = SgcHistoricoRelatorio::where('relatorio_num', $relatorio_num)
                ->where('contrato_id', $contrato_id)
                ->max('versao');
            
            $novaVersao = is_null($ultimaVersaoHistorico) ? 0 : $ultimaVersaoHistorico + 1;

            foreach ($relatorios as $relatorio) {

                $relatorio->update(['status' => 'Revisão Contratada']);

                SgcHistoricoRelatorio::create([
                    'versao' => $novaVersao,
                    'relatorio_num' => $relatorio->relatorio_num,
                    'id_item' => $relatorio->id_item,
                    'nome_topico' => $relatorio->nome_topico,
                    'status' => 'Revisão Contratada',
                    'aprovado' => $relatorio->aprovado,
                    'periodo' => $relatorio->periodo,
                    'contrato_id' => $relatorio->contrato_id,
                    'caminho' => $relatorio->caminho,
                    'created_at' => $relatorio->created_at,
                    'updated_at' => $relatorio->updated_at,
                    'deleted_at' => $relatorio->deleted_at,
                    'data_atualizacao' => $relatorio->data_atualizacao,
                ]);

                $arquivos = SgcRelatorioUpload::where('contrato_id', $contrato_id)
                    ->where('num_relatorio', $relatorio_num)
                    ->where('item_id', $relatorio->id_item)
                    // ->where('versao', $ultimaVersaoHistorico)
                    ->get();

                foreach ($arquivos as $arquivo) {
                    SgcRelatorioUpload::create([
                        'contrato_id' => $arquivo->contrato_id,
                        'nome' => $arquivo->nome,
                        'item_id' => $arquivo->item_id,
                        'caminho' => $arquivo->caminho,
                        'tipo' => $arquivo->tipo,
                        'num_relatorio' => $arquivo->num_relatorio,
                        'versao' => $novaVersao +1,
                    ]);
                }
            }
        });
    }

    
    public function aprovadoStatus($contrato_id, $relatorio_num)
    {
        SgcRelatorioCoordenacao::where('contrato_id', $contrato_id)
            ->where('relatorio_num', $relatorio_num) 
            ->update(['status' => 'Relatório Aprovado']);
    }

    public function consultoriaStatus($contrato_id, $relatorio_num)
    {
        SgcRelatorioCoordenacao::where('contrato_id', $contrato_id)
            ->where('relatorio_num', $relatorio_num)
            ->update(['status' => 'Enviado para consultoria']);
    }
}
