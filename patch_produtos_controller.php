<?php

$content = file_get_contents('/home/leonardo/Documentos/dnit/sgc_developer/app/Domain/Sgc/Contratada/Produtos/Controller/ProdutosController.php');

// 1. Add enviarAnaliseFasePmqa
$enviarAnalise = "
    public function enviarAnaliseFasePmqa(Request \$request, \$contrato, \$produto, \$pmqa): RedirectResponse
    {
        \$data = \$request->validate([
            'fase' => 'required|in:apresentacao,configuracao,execucao,resultado,relatorio'
        ]);

        \$pmqaModel = SgcPmqa::where('id', \$pmqa)
            ->where('id_contrato', \$contrato)
            ->firstOrFail();

        \$campo = 'status_' . \$data['fase'];
        
        abort_unless(\$pmqaModel->{\$campo} === 'Em elaboração' || \$pmqaModel->{\$campo} === 'Reprovada', 422, 'Esta fase não está em elaboração.');

        \$pmqaModel->update([
            \$campo => 'Em análise'
        ]);

        return back()->with('success', 'Fase ' . ucfirst(\$data['fase']) . ' enviada para análise com sucesso!');
    }
";

// 2. Modify aprovarFasePmqa
$aprovarFase = "
    public function aprovarFasePmqa(Request \$request, \$contrato, \$produto, \$pmqa): RedirectResponse
    {
        abort_unless(\$this->usuarioPodeAprovarPmqa(), 403, 'Usuário sem autorização');

        \$data = \$request->validate([
            'fase' => 'required|in:apresentacao,configuracao,execucao,resultado,relatorio'
        ]);

        \$pmqaModel = SgcPmqa::where('id', \$pmqa)
            ->where('id_contrato', \$contrato)
            ->firstOrFail();

        \$campo = 'status_' . \$data['fase'];
        
        abort_unless(\$pmqaModel->{\$campo} === 'Em análise', 422, 'Esta fase não está em análise.');

        \$pmqaModel->update([
            \$campo => 'Aprovada'
        ]);

        // Mapeamento das fases para avançar a próxima
        \$fases = ['apresentacao', 'configuracao', 'execucao', 'resultado', 'relatorio'];
        \$index = array_search(\$data['fase'], \$fases);

        if (\$index !== false && \$index < count(\$fases) - 1) {
            \$proximaFase = \$fases[\$index + 1];
            \$campoProxima = 'status_' . \$proximaFase;
            if (\$pmqaModel->{\$campoProxima} === 'Bloqueado') {
                \$pmqaModel->update([
                    \$campoProxima => 'Em elaboração'
                ]);
            }
        }

        return back()->with('success', 'Fase ' . ucfirst(\$data['fase']) . ' aprovada com sucesso!');
    }
";

// We'll replace the existing aprovarFasePmqa (which might be the old one) or just add it if it doesn't exist.
$content = preg_replace('/public function aprovarFasePmqa.*?return back\(\)->with\([^\)]+\);.*?}/s', $aprovarFase, $content);
// If it didn't exist, we add it before reprovarPmqa
if (strpos($content, 'aprovarFasePmqa') === false) {
    $content = preg_replace('/public function reprovarPmqa/', $aprovarFase . "\n\n    public function reprovarPmqa", $content);
}

// And add enviarAnaliseFasePmqa before reprovarPmqa
if (strpos($content, 'enviarAnaliseFasePmqa') === false) {
    $content = preg_replace('/public function reprovarPmqa/', $enviarAnalise . "\n\n    public function reprovarPmqa", $content);
}


// 3. Modify getCampanhasPmqa
$getCampanhas = "
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
";
$content = preg_replace('/private function getCampanhasPmqa.*?return.*?}\s*}/s', $getCampanhas, $content); // might be tricky with regex

file_put_contents('/home/leonardo/Documentos/dnit/sgc_developer/app/Domain/Sgc/Contratada/Produtos/Controller/ProdutosController.php', $content);

?>
