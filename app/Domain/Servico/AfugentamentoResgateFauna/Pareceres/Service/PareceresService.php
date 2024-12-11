<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Pareceres\Service;

use App\Models\AfugentFaunaRelatorioModel;
use App\Models\ServicoParecerAfugentamentoConfiguracao;
use Illuminate\Support\Facades\DB;

class PareceresService
{
    public function __construct(private readonly ServicoParecerAfugentamentoConfiguracao $servicoParecerAfugentamentoConfiguracao) {}

    public function getPareceres($id_servico)
    {
        $configuracoes = $this->servicoParecerAfugentamentoConfiguracao::select(
            DB::raw("'Configurações' AS tipo"),
            'fk_status',
            DB::raw("parecer COLLATE utf8_general_ci AS parecer"),
            DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y') AS data_parecer")
        )
            ->whereIn('fk_status', [2, 3, 4])
            ->where('fk_servico', $id_servico);

        $relatorios = AfugentFaunaRelatorioModel::select(
            DB::raw("CONCAT('Relatório - ', nome_relatorio) AS tipo"),
            'fk_status',
            DB::raw("parecer_fiscal COLLATE utf8mb4_general_ci AS parecer"),
            DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y') AS data_parecer")
        )
            ->whereIn('fk_status', [2, 3, 4])
            ->where('id_servico', $id_servico);

        $pareceres = $configuracoes->unionAll($relatorios)->paginate(10);

        return $pareceres;
    }
}
