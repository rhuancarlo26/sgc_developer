<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\app\Services;

use App\Models\SgcPmqa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PmqaUpdateService
{
    public function update(Request $request, int $contrato): SgcPmqa
    {
        $data = $request->validate([
            'id'            => 'required|exists:sgc_pmqa,id',
            'cod_emp'       => 'nullable|string',
            'tema'          => 'nullable',
            'especificacao' => 'nullable|string',
            'introducao'    => 'nullable|string',
            'justificativa' => 'nullable|string',
            'objetivos'     => 'nullable|string',
            'metodologia'   => 'nullable|string',
            'publico_alvo'  => 'nullable|string',
        ]);

        $pmqa = SgcPmqa::findOrFail($data['id']);

        $pmqa->update([
            'cod_emp'       => $data['cod_emp'],
            'tema'          => is_array($data['tema']) ? $data['tema']['id'] : $data['tema'],
            'especificacao' => $data['especificacao'],
            'introducao'    => $data['introducao'],
            'justificativa' => $data['justificativa'],
            'objetivos'     => $data['objetivos'],
            'metodologia'   => $data['metodologia'],
            'publico_alvo'  => $data['publico_alvo'],
        ]);

        Log::info('PMQA atualizado com sucesso', [
            'pmqa_id' => $pmqa->id,
            'contrato' => $contrato
        ]);

        return $pmqa;
    }
}
