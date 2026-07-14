<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\OutraAnalise;

use App\Models\AfugentFaunaResultadoOutrasAnalisesModel;

class OutraAnaliseService
{
    public function __construct(private readonly AfugentFaunaResultadoOutrasAnalisesModel $afugentFaunaResultadoOutrasAnalisesModel) {}

    public function create($resultado, $outraAnalise)
    {
        if (!isset($outraAnalise['arquivo']) || !$outraAnalise['arquivo']->isValid()) {
            throw new \Exception('Arquivo não enviado ou inválido.');
        }

        $key = uniqid();
        $caminho = $outraAnalise['arquivo']->storeAs(
            'public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'Afuget_fauna' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'OutrasAnalises',
            $key . $outraAnalise['arquivo']->getClientOriginalName()
        );

        $extensao = $outraAnalise['arquivo']->getClientOriginalExtension();

        return $this->afugentFaunaResultadoOutrasAnalisesModel->create([
            'chave' => $key,
            'id_resultado' => $resultado->id,
            'caminho_arquivo' => $caminho,
            'extensao' => $extensao,
            ...$outraAnalise,
        ]);
    }

    public function update($afugentFaunaResultadoOutrasAnalisesModel, $analise)
    {
        unset($analise['arquivo']);
        return $afugentFaunaResultadoOutrasAnalisesModel->update($analise);
    }

    public function destroy($outraAnalise)
    {
        return $outraAnalise->delete();
    }

    public function getOutraAnalise($resultado)
    {
        return $this->afugentFaunaResultadoOutrasAnalisesModel->where('id_resultado', $resultado->id)->get();
    }
}
