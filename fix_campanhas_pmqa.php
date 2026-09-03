<?php

$path = '/home/leonardo/Documentos/dnit/sgc_developer/app/Domain/Sgc/Contratada/Produtos/Controller/ProdutosController.php';
$content = file_get_contents($path);

$oldGet = <<<OLD
    private function getCampanhasPmqa(\$contrato)
    {
        \$pmqas = SgcPmqa::where('id_contrato', \$contrato)
            ->orderBy('id', 'desc')
            ->get();

        \$pmqaIds = \$pmqas->pluck('id')->toArray();

        \$resumoVinc = \$this->getResumoVinculacoesPmqa(\$pmqaIds);
        \$configuracoes = \$this->getConfiguracoesPmqa(\$pmqaIds);

        return \$pmqas->map(function (\$pmqa) use (\$resumoVinc, \$configuracoes) {
            return [
                'id'             => \$pmqa->id,
                'id_campanha'    => \$pmqa->id,
                'id_contrato'    => \$pmqa->id_contrato,
                'chave'          => \$pmqa->chave ?? null,
                'tipo'           => \$pmqa->tipo ?? 'PMQA',

                'empreendimento' => \$pmqa->cod_emp ?? 'N/A',
                'subproduto'     => \$pmqa->subproduto ?? \$pmqa->tipo ?? 'PMQA',
                'data_inicial'   => \$pmqa->created_at ? \$pmqa->created_at->format('d/m/Y') : 'N/A',
                'data_final'     => 'N/A',
                'status'         => \$pmqa->status_aprovacao ?? 'rascunho',

                'vinculacoesResumo' => \$resumoVinc[\$pmqa->id] ?? [
                    'total_listas' => 0,
                    'total_pontos' => 0,
                    'total_pontos_vinculados' => 0,
                ],
                'configuracao' => \$configuracoes[\$pmqa->id] ?? [
                    'listas' => [],
                    'pontos_sem_lista' => [],
                ],

                'tema'           => \$pmqa->tema,
                'cod_emp'        => \$pmqa->cod_emp,
                'especificacao'  => \$pmqa->especificacao,
                'introducao'     => \$pmqa->introducao,
                'justificativa'  => \$pmqa->justificativa,
                'objetivos'      => \$pmqa->objetivos,
                'metodologia'    => \$pmqa->metodologia,
                'publico_alvo'   => \$pmqa->publico_alvo,

                'status_aprovacao' => \$pmqa->status_aprovacao,
                'created_at'       => optional(\$pmqa->created_at)->toISOString(),
                'updated_at'       => optional(\$pmqa->updated_at)->toISOString(),
                'deleted_at'       => optional(\$pmqa->deleted_at)->toISOString(),
            ];
        });
    }
OLD;

$newGet = <<<NEW
    private function getCampanhasPmqa(\$contrato)
    {
        return SgcPmqa::where('id_contrato', \$contrato)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function (\$pmqa) {
                // Determina o status ativo dinamicamente
                \$fases = ['apresentacao', 'configuracao', 'execucao', 'resultado', 'relatorio'];
                \$statusAtivo = 'Indefinido';
                
                foreach (array_reverse(\$fases) as \$fase) {
                    \$statusFase = \$pmqa->{'status_' . \$fase};
                    if (\$statusFase && \$statusFase !== 'Bloqueado') {
                        \$statusAtivo = ucfirst(\$fase) . ': ' . \$statusFase;
                        break;
                    }
                }
                
                return [
                    'id'               => \$pmqa->id,
                    'nome'             => 'PMQA ' . str_pad(\$pmqa->id, 4, '0', STR_PAD_LEFT),
                    'status'           => \$statusAtivo, // Status dinâmico das fases
                    'status_aprovacao' => \$pmqa->status_aprovacao, // Mantém para retrocompatibilidade
                    'subproduto'       => \$pmqa->subproduto,
                    'is_rascunho'      => \$pmqa->status_apresentacao === 'Em elaboração' || \$pmqa->status_apresentacao === null,
                    'updated_at'       => \$pmqa->updated_at->format('d/m/Y H:i'),
                ];
            });
    }
NEW;

$content = str_replace($oldGet, $newGet, $content);
file_put_contents($path, $content);
echo "Patched getCampanhasPmqa successfully!";
?>
