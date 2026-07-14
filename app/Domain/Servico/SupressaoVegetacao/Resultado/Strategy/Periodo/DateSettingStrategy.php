<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Strategy\Periodo;

interface DateSettingStrategy
{
    public function setDate(array $request): array;
}
