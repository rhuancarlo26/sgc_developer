<?php

namespace App\Domain\Sgc\Licencas\Enums;

/**
 * Situação calculada de uma licença/autorização.
 */
enum SituacaoLicenca: string
{
    case Vigente = 'vigente';
    case AVencer = 'a_vencer';
    case RenovacaoSolicitada = 'renovacao_solicitada';
    case VigenciaProrrogada = 'vigencia_prorrogada';
    case Vencida = 'vencida';
    case Substituida = 'substituida';
    case Arquivada = 'arquivada';

    public function label(): string
    {
        return match ($this) {
            self::Vigente => 'Vigente',
            self::AVencer => 'Renovação próxima',
            self::RenovacaoSolicitada => 'Renovação solicitada',
            self::VigenciaProrrogada => 'Vigência prorrogada (renovação tempestiva)',
            self::Vencida => 'Vencida',
            self::Substituida => 'Substituída',
            self::Arquivada => 'Arquivada',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Vigente => 'success',
            self::AVencer => 'warning',
            self::RenovacaoSolicitada, self::VigenciaProrrogada => 'azure',
            self::Vencida => 'danger',
            self::Substituida, self::Arquivada => 'secondary',
        };
    }

    public static function options(): array
    {
        return array_map(fn (self $c) => ['value' => $c->value, 'label' => $c->label()], self::cases());
    }
}
