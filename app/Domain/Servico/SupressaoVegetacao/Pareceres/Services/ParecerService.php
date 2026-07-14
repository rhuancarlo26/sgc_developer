<?php

namespace App\Domain\Servico\SupressaoVegetacao\Pareceres\Services;

use Illuminate\Support\Facades\DB;

class ParecerService
{

    public function getPareceres($id_servico)
    {
        $firstQuery = DB::table('supressao_parecer_configuracao as ppc')
            ->leftJoin('servicos as s', 'ppc.fk_servico', '=', 's.id')
            ->leftJoin('programas as pm', 's.servico', '=', 'pm.id')
            ->leftJoin('programas_tipo as pt', 's.tipo_servico', '=', 'pt.id')
            ->select([
                'pt.nome as tipo',
                'pm.nome as programa',
                'fk_status',
                DB::raw('(parecer COLLATE utf8_general_ci) as parecer'),
                DB::raw('DATE_FORMAT(ppc.created_at, "%d/%m/%Y") as data_parecer')
            ])
            ->where('ppc.fk_servico', $id_servico);

        $secondQuery = DB::table('supressao_relatorio')
            ->leftJoin('servicos as s', 'supressao_relatorio.fk_servico', '=', 's.id')
            ->leftJoin('programas as pm', 's.servico', '=', 'pm.id')
            ->leftJoin('programas_tipo as pt', 's.tipo_servico', '=', 'pt.id')
            ->select([
                DB::raw('CONCAT("Relatório - ", nome_relatorio) AS tipo'),
                'pm.nome as programa',
                'fk_status',
                DB::raw('parecer_fiscal COLLATE utf8_general_ci'),
                DB::raw('DATE_FORMAT(supressao_relatorio.created_at, "%d/%m/%Y")')
            ])
            ->where('fk_servico', $id_servico);

        return $firstQuery->unionAll($secondQuery)->paginate();

    }
}
