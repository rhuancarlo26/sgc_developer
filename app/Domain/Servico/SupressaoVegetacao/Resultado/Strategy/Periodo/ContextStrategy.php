<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Strategy\Periodo;

use App\Domain\Servico\SupressaoVegetacao\Resultado\Enums\Periodo;
use InvalidArgumentException;

class ContextStrategy
{
    private DateSettingStrategy $strategy;

    public function setStrategy(DateSettingStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function calculate(array $request): array
    {
        match ($request['periodo']) {
            Periodo::ANUAL->value => $this->setStrategy(new AnualStrategy()),
            Periodo::MENSAL->value => $this->setStrategy(new MensalStrategy()),
            Periodo::PERIODO->value => $this->setStrategy(new PeriodoStrategy()),
            Periodo::SEMESTRAL->value => $this->setStrategy(new SemestralStrategy()),
            default => throw new InvalidArgumentException('Período inválido'),
        };

        return $this->strategy->setDate($request);
    }

}
