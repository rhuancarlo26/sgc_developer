<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Strategy\Periodo;

class SemestralStrategy implements DateSettingStrategy
{

    public function setDate(array $request): array
    {
        $semestres = [
            '1' => ['-01-01', '-06-30 23:59:59'],
            '2' => ['-07-01', '-12-31 23:59:59']
        ];
        return [
            'dt_inicio' => date($request['ano'] . $semestres[$request['semestre']][0]),
            'dt_final' => date($request['ano'] . $semestres[$request['semestre']][1])
        ];
    }
}
