<?php

namespace Tests\Unit;

use App\Domain\Sgc\Licencas\Services\PrazoCalculator;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Cenários baseados no RARA da AO de Pernambuco (AO nº 21161207/2024, emitida em 19/11/2024).
 */
class SgcLicencasPrazoCalculatorTest extends TestCase
{
    private PrazoCalculator $calc;
    private CarbonImmutable $emissao;
    private CarbonImmutable $validade;
    private CarbonImmutable $hoje;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calc = new PrazoCalculator();
        $this->emissao = CarbonImmutable::parse('2024-11-19');
        $this->validade = CarbonImmutable::parse('2029-11-19');
        $this->hoje = CarbonImmutable::parse('2026-10-20');
    }

    private function condicionante(array $dados): array
    {
        return array_merge([
            'tipo_prazo' => 'sem_prazo', 'prazo_dias' => null, 'data_fixa' => null,
            'dia_recorrencia' => null, 'mes_recorrencia' => null, 'periodo_ano_anterior' => false,
            'rotulo' => 'Cond. 2.11 - RARA',
        ], $dados);
    }

    public function test_rara_anual_com_periodo_do_ano_anterior(): void
    {
        $ocorrencias = $this->calc->condicionante(
            $this->condicionante(['tipo_prazo' => 'anual', 'dia_recorrencia' => 30, 'mes_recorrencia' => 6, 'periodo_ano_anterior' => true]),
            $this->emissao, $this->validade, $this->hoje
        );

        $this->assertCount(5, $ocorrencias);
        // Primeiro RARA: da emissão da AO a 31/12/2024, entregue até 30/06/2025
        $this->assertSame('2025', $ocorrencias[0]['referencia']);
        $this->assertSame('2025-06-30', $ocorrencias[0]['data_limite']);
        $this->assertSame('2024-11-19', $ocorrencias[0]['periodo_inicio']);
        $this->assertSame('2024-12-31', $ocorrencias[0]['periodo_fim']);
        // Seguintes: janeiro a dezembro do ano anterior
        $this->assertSame('2025-01-01', $ocorrencias[1]['periodo_inicio']);
        $this->assertSame('2025-12-31', $ocorrencias[1]['periodo_fim']);
        $this->assertSame('2029-06-30', $ocorrencias[4]['data_limite']);
    }

    public function test_rcoi_anual_sem_periodo(): void
    {
        $ocorrencias = $this->calc->condicionante(
            $this->condicionante(['tipo_prazo' => 'anual', 'dia_recorrencia' => 31, 'mes_recorrencia' => 5, 'rotulo' => 'RCOI']),
            $this->emissao, $this->validade, $this->hoje
        );

        $this->assertSame(['2025', '2026', '2027', '2028', '2029'], array_column($ocorrencias, 'referencia'));
        $this->assertSame('2026-05-31', $ocorrencias[1]['data_limite']);
        $this->assertNull($ocorrencias[1]['periodo_inicio']);
    }

    public function test_anual_ignora_ano_sem_periodo_valido(): void
    {
        // Emitida em março: o RARA de junho do mesmo ano cobriria um período anterior à emissão
        $ocorrencias = $this->calc->condicionante(
            $this->condicionante(['tipo_prazo' => 'anual', 'dia_recorrencia' => 30, 'mes_recorrencia' => 6, 'periodo_ano_anterior' => true]),
            CarbonImmutable::parse('2025-03-10'), CarbonImmutable::parse('2027-03-10'), $this->hoje
        );

        $this->assertSame(['2026'], array_column($ocorrencias, 'referencia'));
        $this->assertSame('2025-03-10', $ocorrencias[0]['periodo_inicio']);
    }

    public function test_rara_em_ao_retificada_mantem_periodo_desde_a_emissao_original(): void
    {
        // AO 1ª Retificação nº 25624105/2025, emitida em 11/12/2025, sucedendo a AO de 19/11/2024
        $ocorrencias = $this->calc->condicionante(
            $this->condicionante(['tipo_prazo' => 'anual', 'dia_recorrencia' => 30, 'mes_recorrencia' => 6, 'periodo_ano_anterior' => true]),
            CarbonImmutable::parse('2025-12-11'), $this->validade, $this->hoje, $this->emissao
        );

        $this->assertSame('2026', $ocorrencias[0]['referencia']);
        $this->assertSame('2025-01-01', $ocorrencias[0]['periodo_inicio']);
        $this->assertSame('2025-12-31', $ocorrencias[0]['periodo_fim']);
    }

    public function test_anual_sem_validade_vai_ate_o_ano_seguinte(): void
    {
        $ocorrencias = $this->calc->condicionante(
            $this->condicionante(['tipo_prazo' => 'anual', 'dia_recorrencia' => 31, 'mes_recorrencia' => 5]),
            $this->emissao, null, $this->hoje
        );

        $this->assertSame('2027', last($ocorrencias)['referencia']);
    }

    public function test_anual_ajusta_dia_inexistente_no_mes(): void
    {
        $ocorrencias = $this->calc->condicionante(
            $this->condicionante(['tipo_prazo' => 'anual', 'dia_recorrencia' => 29, 'mes_recorrencia' => 2]),
            $this->emissao, $this->validade, $this->hoje
        );

        $this->assertSame('2025-02-28', $ocorrencias[0]['data_limite']);
        $this->assertSame('2028-02-29', $ocorrencias[3]['data_limite']);
    }

    public function test_dias_apos_emissao(): void
    {
        // Cond. 1.5: cronograma em até 90 dias após a emissão
        $ocorrencias = $this->calc->condicionante(
            $this->condicionante(['tipo_prazo' => 'dias_apos_emissao', 'prazo_dias' => 90]),
            $this->emissao, $this->validade, $this->hoje
        );

        $this->assertCount(1, $ocorrencias);
        $this->assertSame('2025-02-17', $ocorrencias[0]['data_limite']);
    }

    public function test_dias_antes_do_vencimento_exige_validade(): void
    {
        // Cond. 2.12: pedido de LO no mínimo 120 dias antes do vencimento
        $cond = $this->condicionante(['tipo_prazo' => 'dias_antes_vencimento', 'prazo_dias' => 120]);

        $this->assertSame('2029-07-22', $this->calc->condicionante($cond, $this->emissao, $this->validade, $this->hoje)[0]['data_limite']);
        $this->assertSame([], $this->calc->condicionante($cond, $this->emissao, null, $this->hoje));
    }

    public function test_sem_prazo_e_apos_evento_nao_geram_ocorrencias(): void
    {
        foreach (['sem_prazo', 'apos_evento'] as $tipo) {
            $cond = $this->condicionante(['tipo_prazo' => $tipo, 'prazo_dias' => 30]);
            $this->assertSame([], $this->calc->condicionante($cond, $this->emissao, $this->validade, $this->hoje));
        }
    }

    public function test_evento(): void
    {
        // Cond. 1.7: relatório 30 dias após o acidente ambiental
        $ocorrencia = $this->calc->evento('Cond. 1.7', CarbonImmutable::parse('2026-03-02'), 30);

        $this->assertSame('evento:2026-03-02', $ocorrencia['referencia']);
        $this->assertSame('2026-04-01', $ocorrencia['data_limite']);
    }

    public function test_renovacao(): void
    {
        $this->assertSame('2029-07-22', $this->calc->renovacao($this->validade, 120)['data_limite']);
        $this->assertNull($this->calc->renovacao(null, 120));
        $this->assertNull($this->calc->renovacao($this->validade, null));
    }
}
