<?php

namespace App\Domain\Sgc\Licencas\Services;

use App\Domain\Sgc\Licencas\Mail\AlertaPrazosMail;
use App\Models\SgcLicencaAlerta;
use App\Models\SgcLicencaPrazo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AlertaService
{
    public function __construct(private readonly PainelService $painelService)
    {
    }

    /**
     * Identifica os prazos pendentes que atingiram um novo marco de alerta e envia um resumo por destinatário.
     * Cada prazo recebe no máximo um alerta por marco; ao vencer, recebe o marco "vencido" uma única vez.
     *
     * @return array{prazos: int, destinatarios: int, sem_destinatario: int}
     */
    public function processar(bool $simular = false): array
    {
        $marcos = collect(config('sgc_licencas.marcos_alerta'))->sort()->values();
        $padrao = config('sgc_licencas.emails_padrao');

        $prazos = SgcLicencaPrazo::query()
            ->where('status', 'pendente')
            ->whereBetween('data_limite', [
                today()->subDays(config('sgc_licencas.alerta_vencidos_ate_dias'))->toDateString(),
                today()->addDays($marcos->max())->toDateString(),
            ])
            ->whereHas('licenca', fn ($q) => $q->ativas())
            ->with(['licenca.tipo', 'licenca.uf', 'licenca.responsaveis:id,name,email', 'condicionante:id,numero'])
            ->get();

        $enviados = SgcLicencaAlerta::whereIn('prazo_id', $prazos->pluck('id'))->get()->groupBy('prazo_id');

        /** @var Collection<string, Collection> $porDestinatario */
        $porDestinatario = collect();
        $disparados = collect();
        $semDestinatario = 0;

        foreach ($prazos as $prazo) {
            $marco = $this->marcoAtual($prazo->diasRestantes(), $marcos);

            if ($marco === null || $enviados->get($prazo->id, collect())->contains('marco', $marco)) {
                continue;
            }

            $emails = $prazo->licenca->responsaveis->pluck('email')->filter();
            $emails = $emails->isNotEmpty() ? $emails : collect($padrao);

            if ($emails->isEmpty()) {
                $semDestinatario++;
                continue;
            }

            foreach ($emails->unique() as $email) {
                $porDestinatario->put($email, $porDestinatario->get($email, collect())->push($this->painelService->resumoPrazo($prazo)));
            }
            $disparados->push(['prazo_id' => $prazo->id, 'marco' => $marco]);
        }

        if (!$simular) {
            foreach ($porDestinatario as $email => $itens) {
                Mail::to($email)->send(new AlertaPrazosMail($itens->sortBy('data_limite')->values()->all()));
            }

            DB::table('sgc_licencas_alertas')->insertOrIgnore(
                $disparados->map(fn ($d) => $d + ['enviado_em' => now()])->all()
            );
        }

        return [
            'prazos' => $disparados->count(),
            'destinatarios' => $porDestinatario->count(),
            'sem_destinatario' => $semDestinatario,
        ];
    }

    /**
     * Menor marco já alcançado (ex.: faltando 20 dias com marcos 90/60/30/15 → "30"); "vencido" após a data-limite.
     */
    private function marcoAtual(int $diasRestantes, Collection $marcos): ?string
    {
        if ($diasRestantes < 0) {
            return 'vencido';
        }

        $marco = $marcos->first(fn ($m) => $diasRestantes <= $m);

        return $marco === null ? null : (string) $marco;
    }
}
