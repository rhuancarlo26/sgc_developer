<?php

namespace App\Domain\Sgc\Licencas\Services;

use App\Domain\Sgc\Licencas\Enums\TipoPrazo;
use Carbon\CarbonImmutable;

/**
 * Calcula as ocorrências de prazo de uma licença e de suas condicionantes.
 * Lógica pura (sem banco), para ser testável isoladamente.
 *
 * Cada ocorrência: ['referencia', 'descricao', 'data_limite', 'ano_referencia', 'periodo_inicio', 'periodo_fim']
 */
class PrazoCalculator
{
    /**
     * Prazo para requerer a renovação: N dias antes do vencimento.
     */
    public function renovacao(?CarbonImmutable $validade, ?int $antecedenciaDias): ?array
    {
        if (!$validade || !$antecedenciaDias) {
            return null;
        }

        return $this->ocorrencia(
            'unico',
            "Requerer renovação ({$antecedenciaDias} dias antes do vencimento)",
            $validade->subDays($antecedenciaDias)
        );
    }

    /**
     * @param array{tipo_prazo: string, prazo_dias: ?int, data_fixa: ?string, dia_recorrencia: ?int,
     *              mes_recorrencia: ?int, periodo_ano_anterior: bool, rotulo: string} $condicionante
     * @param CarbonImmutable $emissao
     * @param CarbonImmutable|null $validade
     * @param CarbonImmutable $hoje Usado para limitar as recorrências de licenças sem validade
     * @param CarbonImmutable|null $inicioCadeia Emissão da primeira licença da cadeia de retificações,
     *                                           usada como início dos períodos de referência
     * @return array
     */
    public function condicionante(
        array $condicionante,
        CarbonImmutable $emissao,
        ?CarbonImmutable $validade,
        CarbonImmutable $hoje,
        ?CarbonImmutable $inicioCadeia = null
    ): array {
        $tipo = TipoPrazo::tryFrom($condicionante['tipo_prazo']) ?? TipoPrazo::SemPrazo;
        $dias = $condicionante['prazo_dias'] ? (int) $condicionante['prazo_dias'] : null;
        $rotulo = $condicionante['rotulo'];

        return match ($tipo) {
            TipoPrazo::DiasAposEmissao => $dias
                ? [$this->ocorrencia('unico', "{$rotulo} ({$dias} dias após a emissão)", $emissao->addDays($dias))]
                : [],
            TipoPrazo::DiasAntesVencimento => $dias && $validade
                ? [$this->ocorrencia('unico', "{$rotulo} ({$dias} dias antes do vencimento)", $validade->subDays($dias))]
                : [],
            TipoPrazo::DataFixa => $condicionante['data_fixa']
                ? [$this->ocorrencia('unico', $rotulo, CarbonImmutable::parse($condicionante['data_fixa']))]
                : [],
            TipoPrazo::Anual => $this->anual($condicionante, $emissao, $validade, $hoje, $inicioCadeia ?? $emissao),
            // Sem prazo ou dependente de evento: nenhuma ocorrência automática
            TipoPrazo::SemPrazo, TipoPrazo::AposEvento => [],
        };
    }

    /**
     * Prazo de uma condicionante "após evento" (ex.: 30 dias após acidente ambiental).
     */
    public function evento(string $rotulo, CarbonImmutable $dataEvento, int $dias): array
    {
        return $this->ocorrencia(
            'evento:' . $dataEvento->format('Y-m-d'),
            "{$rotulo} ({$dias} dias após evento de {$dataEvento->format('d/m/Y')})",
            $dataEvento->addDays($dias)
        );
    }

    /**
     * Uma ocorrência por ano, no dia/mês informado, entre a emissão e o vencimento.
     * Sem vencimento, gera até o ano seguinte ao atual.
     * Com período do ano anterior (ex.: RARA), o período vai de jan a dez do ano anterior,
     * iniciando na emissão quando ela for posterior a 01/01; anos sem período válido são ignorados.
     * Numa retificação, só entram ocorrências a partir da nova emissão, mas o período continua
     * contado desde a emissão original (ex.: RARA de jan a dez entregue sob a AO retificada).
     */
    private function anual(
        array $condicionante,
        CarbonImmutable $emissao,
        ?CarbonImmutable $validade,
        CarbonImmutable $hoje,
        CarbonImmutable $inicioCadeia
    ): array {
        $dia = (int) $condicionante['dia_recorrencia'];
        $mes = (int) $condicionante['mes_recorrencia'];

        if ($dia < 1 || $mes < 1 || $mes > 12) {
            return [];
        }

        $anoFinal = $validade ? $validade->year : $hoje->year + 1;
        $ocorrencias = [];

        for ($ano = $emissao->year; $ano <= $anoFinal; $ano++) {
            $data = CarbonImmutable::create($ano, $mes, 1)->day(min($dia, CarbonImmutable::create($ano, $mes, 1)->daysInMonth));

            if ($data->lt($emissao) || ($validade && $data->gt($validade))) {
                continue;
            }

            $inicio = $fim = null;

            if ($condicionante['periodo_ano_anterior']) {
                $inicio = CarbonImmutable::create($ano - 1, 1, 1)->max($inicioCadeia);
                $fim = CarbonImmutable::create($ano - 1, 12, 31);

                if ($inicio->gt($fim)) {
                    continue;
                }
            }

            $descricao = $inicio
                ? "{$condicionante['rotulo']} {$ano} (período {$inicio->format('d/m/Y')} a {$fim->format('d/m/Y')})"
                : "{$condicionante['rotulo']} {$ano}";

            $ocorrencias[] = $this->ocorrencia((string) $ano, $descricao, $data, $ano, $inicio, $fim);
        }

        return $ocorrencias;
    }

    private function ocorrencia(
        string $referencia,
        string $descricao,
        CarbonImmutable $dataLimite,
        ?int $ano = null,
        ?CarbonImmutable $inicio = null,
        ?CarbonImmutable $fim = null
    ): array {
        return [
            'referencia' => $referencia,
            'descricao' => mb_strimwidth($descricao, 0, 255, '…'),
            'data_limite' => $dataLimite->format('Y-m-d'),
            'ano_referencia' => $ano,
            'periodo_inicio' => $inicio?->format('Y-m-d'),
            'periodo_fim' => $fim?->format('Y-m-d'),
        ];
    }
}
