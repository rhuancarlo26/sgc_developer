<?php 

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\Analise;

use App\Models\AfugentFaunaResultadoAnalisesModel;

class AnaliseService
{

    public function __construct(private readonly AfugentFaunaResultadoAnalisesModel $afugentFaunaResultadoAnalisesModel) {}


    public function create($resultado, $analise)
    {
        return $this->afugentFaunaResultadoAnalisesModel->create([
            'id_resultado' => $resultado->id,
            ... $analise
        ]);
    }

    public function update($afugentFaunaResultadoAnalisesModel, $analise)
    {
        return $afugentFaunaResultadoAnalisesModel->update($analise);
    }

    public function getAnalise($resultado)
    {
        return $this->afugentFaunaResultadoAnalisesModel->where('id_resultado', $resultado->id)->get();
    }
}