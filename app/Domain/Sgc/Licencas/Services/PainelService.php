<?php

namespace App\Domain\Sgc\Licencas\Services;

use App\Domain\Sgc\Licencas\Enums\SituacaoLicenca;
use App\Models\SgcLicenca;
use App\Models\SgcLicencaPrazo;
use App\Models\Uf;
use Illuminate\Database\Eloquent\Builder;

class PainelService
{
    /**
     * Painel geral: indicadores e lista de prazos pendentes (vencidos e próximos 90 dias) das licenças ativas.
     */
    public function painel(array $filtros): array
    {
        $hoje = today();
        $janela = config('sgc_licencas.janela_a_vencer_dias');

        $pendentes = fn () => $this->prazosAtivos($filtros)->where('status', 'pendente');

        $licencas = SgcLicenca::ativas()
            ->with('prazoRenovacao')
            ->tap(fn (Builder $q) => $this->filtrarLicencas($q, $filtros))
            ->get();

        $situacoes = $licencas->map(fn (SgcLicenca $l) => $l->situacao());

        $lista = $pendentes()
            ->with(['licenca:id,tipo_id,numero,uf_id,orgao_emissor', 'licenca.tipo:id,sigla,regularizacao', 'licenca.uf:id,uf', 'condicionante:id,numero'])
            ->whereDate('data_limite', '<=', $hoje->copy()->addDays(90))
            ->orderBy('data_limite')
            ->limit(200)
            ->get()
            ->map(fn (SgcLicencaPrazo $p) => $this->resumoPrazo($p));

        $cumpridosRecentes = $this->prazosAtivos($filtros)
            ->with(['licenca:id,tipo_id,numero,uf_id', 'licenca.tipo:id,sigla', 'licenca.uf:id,uf'])
            ->where('status', 'cumprido')
            ->whereDate('data_cumprimento', '>=', $hoje->copy()->subDays(30))
            ->orderByDesc('data_cumprimento')
            ->limit(10)
            ->get()
            ->map(fn (SgcLicencaPrazo $p) => $this->resumoPrazo($p));

        return [
            'indicadores' => [
                'vencidos' => $pendentes()->whereDate('data_limite', '<', $hoje)->count(),
                'a_vencer' => $pendentes()->whereBetween('data_limite', [$hoje, $hoje->copy()->addDays($janela)])->count(),
                'proximos_90' => $pendentes()->whereBetween('data_limite', [$hoje, $hoje->copy()->addDays(90)])->count(),
                'licencas_ativas' => $licencas->count(),
                'licencas_vencidas' => $situacoes->filter(fn ($s) => $s === SituacaoLicenca::Vencida)->count(),
                'renovacoes_proximas' => $situacoes->filter(fn ($s) => $s === SituacaoLicenca::AVencer)->count(),
            ],
            'prazos' => $lista,
            'cumpridos_recentes' => $cumpridosRecentes,
            'janela' => $janela,
        ];
    }

    /**
     * Regularização ambiental: uma linha por UF com a AO ativa e a situação dos relatórios periódicos no ano.
     */
    public function regularizacao(int $ano): array
    {
        $aos = SgcLicenca::ativas()
            ->regularizacao()
            ->with([
                'tipo:id,sigla',
                'prazoRenovacao',
                'prazos' => fn ($q) => $q->where('ano_referencia', $ano)
                    ->whereHas('condicionante', fn ($c) => $c->whereNotNull('relatorio_sigla'))
                    ->with('condicionante:id,numero,relatorio_sigla'),
            ])
            ->get()
            ->groupBy('uf_id');

        $siglas = $aos->flatten()
            ->flatMap(fn (SgcLicenca $ao) => $ao->prazos->pluck('condicionante.relatorio_sigla'))
            ->unique()
            ->sortBy(fn ($sigla) => [array_search($sigla, ['RCOI', 'RARA']) === false ? 99 : array_search($sigla, ['RCOI', 'RARA']), $sigla])
            ->values();

        $linhas = Uf::orderBy('uf')->get(['id', 'uf', 'nome'])->map(function (Uf $uf) use ($aos) {
            $doEstado = $aos->get($uf->id, collect());

            return [
                'uf_id' => $uf->id,
                'uf' => $uf->uf,
                'nome' => $uf->nome,
                'aos' => $doEstado->map(fn (SgcLicenca $ao) => [
                    'id' => $ao->id,
                    'numero' => $ao->numero,
                    'sigla' => $ao->tipo?->sigla,
                    'data_emissao' => $ao->data_emissao?->format('Y-m-d'),
                    'data_validade' => $ao->data_validade?->format('Y-m-d'),
                    'situacao' => ['label' => $ao->situacao()->label(), 'cor' => $ao->situacao()->cor()],
                    'relatorios' => $ao->prazos
                        ->groupBy('condicionante.relatorio_sigla')
                        ->map(fn ($prazos) => $prazos->map(fn (SgcLicencaPrazo $p) => $this->resumoPrazo($p))->values()),
                ])->values(),
            ];
        });

        $primeiroAno = SgcLicenca::regularizacao()->min('data_emissao');

        return [
            'ano' => $ano,
            'anos' => range((int) ($primeiroAno ? substr($primeiroAno, 0, 4) : now()->year), now()->year + 1),
            'siglas' => $siglas,
            'linhas' => $linhas,
        ];
    }

    public function resumoPrazo(SgcLicencaPrazo $prazo): array
    {
        $situacao = $prazo->situacao();

        return [
            ...$prazo->only(['id', 'licenca_id', 'condicionante_id', 'origem', 'referencia', 'descricao', 'ano_referencia', 'status']),
            'data_limite' => $prazo->data_limite?->format('Y-m-d'),
            'data_limite_original' => $prazo->data_limite_original?->format('Y-m-d'),
            'data_cumprimento' => $prazo->data_cumprimento?->format('Y-m-d'),
            'data_evento' => $prazo->data_evento?->format('Y-m-d'),
            'periodo_inicio' => $prazo->periodo_inicio?->format('Y-m-d'),
            'periodo_fim' => $prazo->periodo_fim?->format('Y-m-d'),
            'prorrogado' => $prazo->foiProrrogado(),
            'dias_restantes' => $prazo->diasRestantes(),
            'situacao' => ['valor' => $situacao->value, 'label' => $situacao->label(), 'cor' => $situacao->cor()],
            'licenca' => $prazo->relationLoaded('licenca') && $prazo->licenca ? [
                'id' => $prazo->licenca->id,
                'rotulo' => $prazo->licenca->rotulo(),
                'uf' => $prazo->licenca->uf?->uf,
                'orgao_emissor' => $prazo->licenca->orgao_emissor,
                'regularizacao' => (bool) $prazo->licenca->tipo?->regularizacao,
            ] : null,
            'condicionante_numero' => $prazo->relationLoaded('condicionante') ? $prazo->condicionante?->numero : null,
        ];
    }

    private function prazosAtivos(array $filtros): Builder
    {
        return SgcLicencaPrazo::query()->whereHas('licenca', function (Builder $q) use ($filtros) {
            $q->ativas();
            $this->filtrarLicencas($q, $filtros);
        });
    }

    private function filtrarLicencas(Builder $query, array $filtros): void
    {
        $query
            ->when(isset($filtros['regularizacao']) && $filtros['regularizacao'] !== '', fn ($q) => $q->regularizacao((bool) $filtros['regularizacao']))
            ->when($filtros['uf_id'] ?? null, fn ($q, $uf) => $q->where('uf_id', $uf))
            ->when($filtros['orgao_emissor'] ?? null, fn ($q, $orgao) => $q->where('orgao_emissor', $orgao));
    }
}
