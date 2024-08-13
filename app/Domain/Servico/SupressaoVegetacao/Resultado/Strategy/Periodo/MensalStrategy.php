<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Strategy\Periodo;

class MensalStrategy implements DateSettingStrategy
{

    public function setDate(array $request): array
    {
        $inicio = $request['mes'] . '-01';
        return [
            'dt_inicio' => $inicio,
            'dt_final' => date("Y-m-t", strtotime($inicio)) . ' 23:59:59'
        ];
    }
}
