<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\app\Services;

use App\Models\SgcPmqa;
use Illuminate\Support\Facades\Auth;

class PmqaApprovalService
{
    public function approve(int $contrato, int $pmqaId): SgcPmqa
    {
        $pmqa = $this->findPmqaOrFail($contrato, $pmqaId);

        $pmqa->update([
            'status_aprovacao' => 'Em elaboração',
            'aprovado_por'     => Auth::user()->name,
            'aprovado_em'      => now(),
        ]);

        return $pmqa;
    }

    public function reprove(int $contrato, int $pmqaId, ?string $motivo = null): SgcPmqa
    {
        $pmqa = $this->findPmqaOrFail($contrato, $pmqaId);

        $pmqa->update([
            'status_aprovacao'  => 'Reprovado',
            'reprovado_por'     => Auth::id(),
            'reprovado_em'      => now(),
            'motivo_reprovacao' => $motivo,
        ]);

        return $pmqa;
    }

    private function findPmqaOrFail(int $contrato, int $pmqaId): SgcPmqa
    {
        return SgcPmqa::where('id', $pmqaId)
            ->where('id_contrato', $contrato)
            ->firstOrFail();
    }
}
