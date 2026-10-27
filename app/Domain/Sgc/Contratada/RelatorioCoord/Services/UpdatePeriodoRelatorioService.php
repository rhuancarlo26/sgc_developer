<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Services;

use App\Models\ChangeLog;
use App\Models\SgcHistoricoRelatorio;
use App\Models\SgcRelatorioCoordenacao;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class UpdatePeriodoRelatorioService
{
    public function atualizar($contratoId, $numero, string $inicio, string $fim, int $usuarioId): void
    {
        $periodo = CarbonImmutable::parse($inicio)->format('d/m/Y').' a '.CarbonImmutable::parse($fim)->format('d/m/Y');
        DB::transaction(function () use ($contratoId, $numero, $periodo, $usuarioId) {
            $atuais = SgcRelatorioCoordenacao::where('contrato_id', $contratoId)
                ->where('relatorio_num', $numero)->lockForUpdate()->get();
            abort_if($atuais->isEmpty(), 404);
            $historicos = SgcHistoricoRelatorio::where('contrato_id', $contratoId)
                ->where('relatorio_num', $numero)->lockForUpdate()->get();
            foreach ($atuais->concat($historicos) as $registro) {
                if ($registro->periodo === $periodo) continue;
                $anterior = $registro->periodo;
                // Correcao de metadado: preserva os instantes originais das revisoes.
                $registro->timestamps = false;
                $registro->update(['periodo' => $periodo]);
                ChangeLog::create([
                    'user_id' => $usuarioId, 'table_name' => $registro->getTable(),
                    'record_id' => $registro->id, 'field' => 'periodo',
                    'old_value' => $anterior, 'new_value' => $periodo,
                ]);
            }
        });
    }
}
