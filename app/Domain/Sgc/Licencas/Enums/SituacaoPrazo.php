<?php

namespace App\Domain\Sgc\Licencas\Enums;

/**
 * Situação calculada de um prazo, a partir do status gravado e da data-limite.
 */
enum SituacaoPrazo: string
{
    case NoPrazo = 'no_prazo';
    case AVencer = 'a_vencer';
    case Vencido = 'vencido';
    case Cumprido = 'cumprido';
    case CumpridoComAtraso = 'cumprido_atraso';
    case Dispensado = 'dispensado';

    public function label(): string
    {
        return match ($this) {
            self::NoPrazo => 'No prazo',
            self::AVencer => 'A vencer',
            self::Vencido => 'Vencido',
            self::Cumprido => 'Cumprido',
            self::CumpridoComAtraso => 'Cumprido com atraso',
            self::Dispensado => 'Dispensado',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::NoPrazo => 'azure',
            self::AVencer => 'warning',
            self::Vencido => 'danger',
            self::Cumprido => 'success',
            self::CumpridoComAtraso => 'lime',
            self::Dispensado => 'secondary',
        };
    }
}
