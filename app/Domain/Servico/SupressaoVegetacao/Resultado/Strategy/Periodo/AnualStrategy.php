<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Strategy\Periodo;

class AnualStrategy implements DateSettingStrategy
{

    public function setDate(array $request): array
    {
        return [
            'dt_inicio' => date($request['ano'] . '-01-01'),
            'dt_final' => date($request['ano'] . '-12-31') . ' 23:59:59',
        ];
    }
}
