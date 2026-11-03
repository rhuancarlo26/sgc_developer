<?php

namespace App\Domain\Sgc\Licencas\Enums;

enum TipoRegistro: string
{
    case Cumprimento = 'cumprimento';
    case Prorrogacao = 'prorrogacao';
    case Dispensa = 'dispensa';
    case Observacao = 'observacao';

    public function label(): string
    {
        return match ($this) {
            self::Cumprimento => 'Cumprimento (protocolo/ofício)',
            self::Prorrogacao => 'Prorrogação de prazo',
            self::Dispensa => 'Dispensa / não aplicável',
            self::Observacao => 'Observação',
        };
    }

    public static function options(): array
    {
        return array_map(fn (self $c) => ['value' => $c->value, 'label' => $c->label()], self::cases());
    }
}
