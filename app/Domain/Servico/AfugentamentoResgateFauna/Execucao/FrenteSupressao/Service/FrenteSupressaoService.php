<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Service;

use App\Models\AfugentFaunaExecFrenteModel;

class FrenteSupressaoService
{
    public function create($request, $servico): AfugentFaunaExecFrenteModel
    {
        return AfugentFaunaExecFrenteModel::create([
            ...$request,
            'id_servico' => $servico->id
        ]);
    }

    public function update($request, $frente)
    {
        $frente->update([
            ...$request,
            'rodovia' => $request['rodovia'],
            'uf_inicial' => $request['uf_inicial'],
            'uf_final' => $request['uf_final'],
        ]);
    }

    public function delete(AfugentFaunaExecFrenteModel $frente): void
    {
        $frente->delete();
    }

    public function getFrenteSupressao($servico)
    {
        return AfugentFaunaExecFrenteModel::where('id_servico', $servico->id)->get();
    }
}
