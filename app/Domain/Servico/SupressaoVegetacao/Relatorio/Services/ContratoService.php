<?php

namespace App\Domain\Servico\SupressaoVegetacao\Relatorio\Services;

use App\Models\Contrato;

class ContratoService
{

    public static function getContratoRelatorio($servicoId)
    {
        return Contrato::select([
            'base_rodovia.rodovia',
            'contratos.numero_contrato',
            'contratos.contratada',
            'estados.uf'
        ])
            ->join('servicos AS s', 's.id_contrato', '=', 'contratos.id')
            ->join('estados', 'estados.id', '=', 'contratos.id_uf')
            ->join('base_rodovia', 'base_rodovia.id', '=', 'contratos.id_rodovia')
            ->where('s.id', $servicoId)
            ->first();
    }
}
