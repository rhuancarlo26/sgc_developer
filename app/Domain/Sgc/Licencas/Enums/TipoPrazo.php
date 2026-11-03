<?php

namespace App\Domain\Sgc\Licencas\Enums;

enum TipoPrazo: string
{
    case SemPrazo = 'sem_prazo';
    case DiasAposEmissao = 'dias_apos_emissao';
    case DiasAntesVencimento = 'dias_antes_vencimento';
    case DataFixa = 'data_fixa';
    case Anual = 'anual';
    case AposEvento = 'apos_evento';

    public function label(): string
    {
        return match ($this) {
            self::SemPrazo => 'Sem prazo (informativa/contínua)',
            self::DiasAposEmissao => 'Dias após a emissão',
            self::DiasAntesVencimento => 'Dias antes do vencimento',
            self::DataFixa => 'Data fixa',
            self::Anual => 'Anual (dia/mês fixo)',
            self::AposEvento => 'Dias após um evento',
        };
    }

    public static function options(): array
    {
        return array_map(fn (self $c) => ['value' => $c->value, 'label' => $c->label()], self::cases());
    }
}
