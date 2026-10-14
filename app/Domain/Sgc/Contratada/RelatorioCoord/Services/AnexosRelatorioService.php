<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Services;

use App\Models\SgcHistoricoRelatorio;
use App\Models\SgcRelatorioUpload;
use Illuminate\Database\Eloquent\Builder;

class AnexosRelatorioService
{
    public static function versaoAtual($contratoId, $relatorioNum): int
    {
        $ultima = SgcHistoricoRelatorio::where('contrato_id', $contratoId)
            ->where('relatorio_num', $relatorioNum)->max('versao');

        return $ultima === null ? 0 : (int) $ultima + 1;
    }

    public static function consulta($contratoId, $relatorioNum, $versao = null): Builder
    {
        // A mesma prioridade deve ser usada no upload, na copia e na leitura.
        // Duplicatas antigas sao preservadas; prevalece o registro atualizado.
        return SgcRelatorioUpload::where('contrato_id', $contratoId)
            ->where('num_relatorio', $relatorioNum)
            ->where('versao', $versao ?? self::versaoAtual($contratoId, $relatorioNum))
            ->orderByDesc('updated_at')->orderByDesc('id');
    }
}
