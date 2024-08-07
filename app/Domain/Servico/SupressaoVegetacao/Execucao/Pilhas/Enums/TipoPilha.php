<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Enums;

enum TipoPilha: int
{
    case ESPECIE_COMUM = 0;
    case ESPECIE_AMEACADA_PROTEGIDA = 1;
    case MATERIA_ORGANICA = 2;

    public static function getLabel(int $value): string
    {
        return match ($value) {
            self::ESPECIE_COMUM->value => 'Espécie Comum',
            self::ESPECIE_AMEACADA_PROTEGIDA->value => 'Espécie Ameaçada/Protegida',
            self::MATERIA_ORGANICA->value => 'Matéria Orgânica',
            default => 'Valor desconhecido',
        };
    }

    public static function toArray(): array
    {
        return array_map(function (TipoPilha $pilha) {
            return [
                'id' => $pilha->value,
                'label' => self::getLabel($pilha->value),
            ];
        }, self::cases());
    }
}
