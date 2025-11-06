<?php

namespace App\Domain\Servico\PassagemFauna\Pareceres\Service;

use App\Models\ServicoPassagemFaunaRelatorio;
use App\Models\ServicoPassagemFaunaParecerConfiguracao;
use Illuminate\Support\Facades\DB;

class PareceresService
{
    public function __construct(private readonly ServicoPassagemFaunaParecerConfiguracao $ServicoPassagemFaunaParecerConfiguracao) {}

    public function getPareceres($id_servico)
    {
        $configuracoes = $this->ServicoPassagemFaunaParecerConfiguracao::select(
            DB::raw("'Configurações' AS tipo"),
            'fk_status',
            DB::raw("CONVERT(parecer USING utf8mb4) COLLATE utf8mb4_general_ci AS parecer"),
            DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y') AS data_parecer")
        )
            ->whereIn('fk_status', [2, 3, 4])
            ->where('fk_servico', $id_servico);

        $relatorios = ServicoPassagemFaunaRelatorio::select(
            DB::raw("CONCAT('Relatório - ', nome_relatorio) AS tipo"),
            'fk_status',
            DB::raw("CONVERT(parecer_fiscal USING utf8mb4) COLLATE utf8mb4_general_ci AS parecer"),
            DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y') AS data_parecer")
        )
            ->whereIn('fk_status', [2, 3, 4])
            ->where('id_servico', $id_servico);

        $pareceres = $configuracoes->unionAll($relatorios)->paginate(10);

        return $pareceres;
    }
}
