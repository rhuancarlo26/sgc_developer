<?php

namespace App\Domain\Sgc\Licencas\Enums;

enum SituacaoCondicionante: string
{
    case Atendida = 'atendida';
    case ParcialmenteAtendida = 'parcialmente_atendida';
    case EmAtendimento = 'em_atendimento';
    case NaoAtendida = 'nao_atendida';
    case Informativa = 'informativa';

    public function label(): string
    {
        return match ($this) {
            self::Atendida => 'Atendida',
            self::ParcialmenteAtendida => 'Parcialmente atendida',
            self::EmAtendimento => 'Em atendimento',
            self::NaoAtendida => 'Não atendida',
            self::Informativa => 'Informativa/orientativa',
        };
    }

    public static function options(): array
    {
        return array_map(fn (self $c) => ['value' => $c->value, 'label' => $c->label()], self::cases());
    }
}
