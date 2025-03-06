<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Enums;

enum Periodo: string
{
    case ANUAL = 'A';
    case MENSAL = 'M';
    case PERIODO = 'P';
    case SEMESTRAL = 'S';

    public static function getLabel(?string $value): string
    {
        return match ($value) {
            self::ANUAL->value => 'Anual',
            self::MENSAL->value => 'Mensal',
            self::PERIODO->value => 'Período',
            self::SEMESTRAL->value => 'Semestral',
            default => 'Valor desconhecido',
        };
    }

    public static function toArray(): array
    {
        return array_map(function (Periodo $periodo) {
            return [
                'id' => $periodo->value,
                'label' => self::getLabel($periodo->value),
            ];
        }, self::cases());
    }
}
