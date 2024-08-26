<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\ControleRNC\Services;

use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaHistorico;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ControleRNCService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoConOcorrOcorrenciSupervisaoExecOcorrencia::class;
    protected string $modelClassHistorico = ServicoConOcorrSupervisaoExecOcorrenciaHistorico::class;

    public function index($servico_id, array $searchParams): array
    {
        return [
            'ocorrencias' => $this->searchAllColumns(...$searchParams)
                ->with(['lote', 'rodovia.uf', 'registros', 'historico.levantamento', 'vistorias'])
                ->where('id_servico', $servico_id)->where('tipo', '<>', 'Não Atendida')->where('envio_fiscal', '<>', null)
                ->paginate()
                ->appends($searchParams),
            'ocorrencias_fiscal' => $this->modelClass::with(['lote', 'rodovia.uf', 'registros', 'historico.levantamento'])
                ->where('id_servico', $servico_id)->where('tipo', '<>', 'Não Atendida')->where('envio_fiscal', '<>', null)->get()
        ];
    }

    public function update(array $post)
    {
        $response = [
            'request' => [
                'type' => 'error',
                'content' => 'Falha ao cadastrar!'
            ]
        ];

        foreach ($post['ocorrencias'] as $ocorrencia) {
            try {
                $response = $this->dataManagement->update(entity: $this->modelClass, infos: [
                    'envio_empresa' => 'Sim'
                ], id: $ocorrencia['id']);

                $this->dataManagement->create(entity: $this->modelClassHistorico, infos: [
                    'id_ocorrencia' => $ocorrencia['id'],
                    'id_ocorrencia_levantamento' => 8
                ]);
            } catch (\Exception $ex) {

            }
        }

        return $response;
    }
}
