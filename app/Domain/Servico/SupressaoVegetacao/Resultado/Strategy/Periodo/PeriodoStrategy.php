<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Strategy\Periodo;

class PeriodoStrategy implements DateSettingStrategy
{

    public function setDate(array $request): array
    {
        return [
            'dt_inicio' => date($request['dt_inicio']),
            'dt_final' => date($request['dt_final']) . ' 23:59:59'
        ];
    }
}
