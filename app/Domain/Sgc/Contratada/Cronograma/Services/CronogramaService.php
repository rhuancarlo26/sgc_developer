<?php

namespace App\Domain\Sgc\Contratada\Cronograma\Services;

use App\Models\SgcvwEstudos;
use App\Models\SgcCronogramaAuxiliar;
use App\Models\Contrato;

class CronogramaService
{
    public function obterCronograma(Contrato $contrato)
    {
        $eventosPrincipais = SgcvwEstudos::where('contrato_id', $contrato->id)
            ->select(
                'cod_emp',
                'uf',
                'br',
                'subproduto',
                'data_de_inicio_previsto',
                'data_de_entrega_previsto',
                'versao_00_data_de_entrega',
                'contrato',
                'empresa',
                'etapa',
                'item_edital',
                'versao_aceita_data',
                'req_ext_data',
                'aut_ext_data'
            )
            ->get()
            ->map(function ($evento) {
                return [
                    'title' => $evento->subproduto,
                    'start' => $evento->data_de_inicio_previsto,
                    'end' => $evento->data_de_entrega_previsto,
                    'cod_emp' => $evento->cod_emp,
                    'uf' => $evento->uf,
                    'versao_00_data_de_entrega' => $evento->versao_00_data_de_entrega,
                    'contrato' => $evento->contrato,
                    'empresa' => $evento->empresa,
                    'etapa' => $evento->etapa,
                    'item_edital' => $evento->item_edital,
                    'versao_aceita_data' => $evento->versao_aceita_data,
                    'req_ext_data' => $evento->req_ext_data,
                    'aut_ext_data' => $evento->aut_ext_data,
                    'source' => 'principal',
                    'backgroundColor' => '#3788d8', // Azul para eventos principais
                ];
            });

        $eventosAuxiliares = SgcCronogramaAuxiliar::where('contrato_id', $contrato->id)
            ->select(
                'cod_emp',
                'contrato',
                'item_edital',
                'subproduto',
                'data_de_inicio_previsto',
                'data_de_entrega_previsto'
            )
            ->get()
            ->map(function ($evento) {
                return [
                    'title' => $evento->subproduto,
                    'start' => $evento->data_de_inicio_previsto,
                    'end' => $evento->data_de_entrega_previsto,
                    'cod_emp' => $evento->cod_emp,
                    'contrato' => $evento->contrato,
                    'item_edital' => $evento->item_edital,
                    'source' => 'auxiliar',
                    'backgroundColor' => '#28a745', // Verde fixo para eventos auxiliares
                ];
            });

        return $eventosPrincipais->merge($eventosAuxiliares);
    }

    public function obterOpcoesEvento(Contrato $contrato)
    {
        $empreendimentos = SgcvwEstudos::where('contrato_id', $contrato->id)
            ->distinct()
            ->pluck('cod_emp');

        $subprodutos = SgcvwEstudos::where('contrato_id', $contrato->id)
            ->distinct()
            ->pluck('subproduto');

        return [
            'empreendimentos' => $empreendimentos,
            'subprodutos' => $subprodutos,
        ];
    }
    public function salvarEventoAuxiliar(array $data)
    {
        return SgcCronogramaAuxiliar::create($data);
    }
}