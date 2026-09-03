<?php

$path = '/home/leonardo/Documentos/dnit/sgc_developer/app/Domain/Sgc/Contratada/Produtos/Controller/ProdutosController.php';
$content = file_get_contents($path);

// 1. Fix createPmqa
$content = str_replace(
    "\$pmqa = SgcPmqa::create([\n                'id_contrato'  => \$contrato,\n                'subproduto'   => \$subproduto,\n                'status_aprovacao' => 'Em elaboração',\n            ]);",
    "\$pmqa = new SgcPmqa([\n                'id_contrato'  => \$contrato,\n                'subproduto'   => \$subproduto,\n                'status_aprovacao' => 'Em elaboração',\n            ]);",
    $content
);

// 2. Fix updatePmqa (removing cod_emp 'string', fixing redirect, and supporting create)
$oldUpdate = <<<OLD
    public function updatePmqa(Request \$request, \$contrato, \$produto = 'eia')
    {
        \$data = \$request->validate([
            'id'            => 'required|exists:sgc_pmqa,id',
            'cod_emp'       => 'nullable|string',
            'tema'          => 'nullable',
            'especificacao' => 'nullable|string',
            'introducao'    => 'nullable|string',
            'justificativa' => 'nullable|string',
            'objetivos'     => 'nullable|string',
            'metodologia'   => 'nullable|string',
            'publico_alvo'  => 'nullable|string',
        ]);

        \$pmqa = SgcPmqa::findOrFail(\$data['id']);

        \$pmqa->update([
            'cod_emp'       => \$data['cod_emp'],
            'tema'          => is_array(\$data['tema']) ? \$data['tema']['id'] : \$data['tema'],
            'especificacao' => \$data['especificacao'],
            'introducao'    => \$data['introducao'],
            'justificativa' => \$data['justificativa'],
            'objetivos'     => \$data['objetivos'],
            'metodologia'   => \$data['metodologia'],
            'publico_alvo'  => \$data['publico_alvo'],
            'status_aprovacao' => 'Em análise',
        ]);

        return redirect()
            ->route('sgc.contratada.produtos.index', [
                'contrato'   => \$contrato,
                'produto'    => \$produto,
                'subproduto' => \$pmqa->subproduto,
            ])
            ->with('success', 'Apresentação do PMQA submetida para análise.');
    }
OLD;

$newUpdate = <<<NEW
    public function updatePmqa(Request \$request, \$contrato, \$produto = 'eia')
    {
        \$data = \$request->validate([
            'id'            => 'nullable|exists:sgc_pmqa,id',
            'cod_emp'       => 'nullable',
            'tema'          => 'nullable',
            'especificacao' => 'nullable|string',
            'introducao'    => 'nullable|string',
            'justificativa' => 'nullable|string',
            'objetivos'     => 'nullable|string',
            'metodologia'   => 'nullable|string',
            'publico_alvo'  => 'nullable|string',
        ]);

        if (isset(\$data['id'])) {
            \$pmqa = SgcPmqa::findOrFail(\$data['id']);
            \$pmqa->update([
                'cod_emp'       => \$data['cod_emp'],
                'tema'          => is_array(\$data['tema']) ? \$data['tema']['id'] : \$data['tema'],
                'especificacao' => \$data['especificacao'],
                'introducao'    => \$data['introducao'],
                'justificativa' => \$data['justificativa'],
                'objetivos'     => \$data['objetivos'],
                'metodologia'   => \$data['metodologia'],
                'publico_alvo'  => \$data['publico_alvo'],
            ]);
        } else {
            SgcPmqa::create([
                'id_contrato'   => \$contrato,
                'subproduto'    => \$request->input('subproduto') ?? 'EIA',
                'status_aprovacao' => 'Em elaboração',
                'cod_emp'       => \$data['cod_emp'],
                'tema'          => is_array(\$data['tema']) ? \$data['tema']['id'] : \$data['tema'],
                'especificacao' => \$data['especificacao'],
                'introducao'    => \$data['introducao'],
                'justificativa' => \$data['justificativa'],
                'objetivos'     => \$data['objetivos'],
                'metodologia'   => \$data['metodologia'],
                'publico_alvo'  => \$data['publico_alvo'],
            ]);
        }

        return back()->with('success', 'Apresentação do PMQA salva com sucesso.');
    }
NEW;

$content = str_replace($oldUpdate, $newUpdate, $content);

// 3. Add missing endpoints (aprovarFasePmqa, enviarAnaliseFasePmqa) before reprovarPmqa
$enviarAnalise = <<<EOT
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
EOT;

if (strpos($content, 'enviarAnaliseFasePmqa') === false) {
    $content = str_replace('public function reprovarPmqa', $enviarAnalise . "\n\n    public function reprovarPmqa", $content);
}

file_put_contents($path, $content);
echo "Patched successfully!";
?>
