<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Services;

use App\Models\SgcRelatorioUpload;
use App\Models\SgcRelatorioCoordenacao;
use Illuminate\Support\Facades\DB;

class CreateRelatorioService
{
    public function iniciarNovoRelatorio($contrato)
    {
        DB::transaction(function () use ($contrato) {
            // Acesse o ID do contrato
            $contratoId = $contrato['id'];

            // Buscar o último número de relatório para o contrato
            $ultimoRelatorioNum = SgcRelatorioCoordenacao::where('contrato_id', $contratoId)
                ->max('relatorio_num');


            $novoRelatorioNum = $ultimoRelatorioNum ? $ultimoRelatorioNum + 1 : 1;

            $itensPadrao = [
                ['id_item' => 0, 'nome_topico' => '0 - Capa'],
                ['id_item' => 1, 'nome_topico' => '1 - Apresentação'],
                ['id_item' => 2, 'nome_topico' => '2 - Equipe de Coordenação da Execução dos Serviços'],
                ['id_item' => 3, 'nome_topico' => '3 - Diagnóstico dos Empreendimentos Prioritários'],
                ['id_item' => 4, 'nome_topico' => '4 - Sistema de Gestão do Contrato (SGC)'],
                ['id_item' => 5, 'nome_topico' => '5 - Resumo das Atividades Desenvolvidas no Período'],
                ['id_item' => 6, 'nome_topico' => '6 - Planejamento Anual das Atividades de Geoprocessamento'],
                ['id_item' => 7, 'nome_topico' => '7 - Evolução das Etapas do Licenciamento Ambiental'],
                ['id_item' => 8, 'nome_topico' => '8 - Comparativo entre Metas Previstas e Realizadas'],
                ['id_item' => 9, 'nome_topico' => '9 - Reuniões de Diligenciamento'],
                ['id_item' => 10, 'nome_topico' => '10 - Indicadores Contratuais'],
                ['id_item' => 11, 'nome_topico' => '11 - Monitoramento Contratual'],
                ['id_item' => 12, 'nome_topico' => '12 - Execução Contratual'],
                ['id_item' => 13, 'nome_topico' => '13 - Pendências e Recomendações para Manutenção Contratual'],
                ['id_item' => 14, 'nome_topico' => '14 - Considerações Finais'],
                ['id_item' => 15, 'nome_topico' => '15 - Assinatura dos Responsáveis pelo Subproduto'],
                ['id_item' => 16, 'nome_topico' => '16 - Termo de Encerramento'],
                ['id_item' => 18, 'nome_topico' => 'ANEXO I'],
                ['id_item' => 19, 'nome_topico' => 'ANEXO II'],
                ['id_item' => 20, 'nome_topico' => 'ANEXO III'],
            ];

            // Insere os itens no banco
            foreach ($itensPadrao as $item) {
                $dados = [
                    'relatorio_num' => $novoRelatorioNum,
                    'id_item' => $item['id_item'],
                    'nome_topico' => $item['nome_topico'],
                    'status' => 'Em Elaboração',
                    'aprovado' => 0,
                    'periodo' => '01/01/2000 a 01/01/2000',
                    'contrato_id' => $contratoId,
                    'versao' => 0,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];

                SgcRelatorioCoordenacao::create($dados);
            }
        });


        return response()->json(['message' => 'Relatório criado com sucesso!'], 201);

    }
}
