<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\ACA\Services;

use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervisaoExecAca;
use App\Models\ServicoContOcorrSupervisaoConfigLote;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ACAService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoConOcorrSupervisaoExecAca::class;
    protected string $modelClassLote = ServicoContOcorrSupervisaoConfigLote::class;
    protected string $modelClassOcorrencia = ServicoConOcorrOcorrenciSupervisaoExecOcorrencia::class;

    public function index(int $servico_id, array $searchParams): array
    {
        return [
            'acas' => $this->searchAllColumns(...$searchParams)
                ->with(['servico.contrato', 'lote', 'rncs.lote'])
                ->where('id_servico', $servico_id)
                ->paginate()
                ->appends($searchParams),
            'acas_nao_enviadas' => $this->modelClass::with(['lote', 'rncs'])->where('id_servico', $servico_id)->where('enviado', 'Não')->get(),
            'lotes' => $this->modelClassLote::where('id_servico', $servico_id)->get(),
            'ocorrencias_aprovadas' => $this->modelClassOcorrencia::with(['lote'])->where('id_servico', $servico_id)->whereDoesntHave('rncs')->get()
        ];
    }

    public function store(array $post): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $post);

        $response['model']->rncs()->sync(collect($post['ocorrencias'])->pluck('id')->toArray());

        return $response;
    }

    public function destroy(int $aca_id): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $aca_id);
    }

    public function enviarACA(array $post)
    {
        $response = [
            'request' => [
                'type' => 'error',
                'content' => 'Falha ao cadastrar!'
            ]
        ];

        foreach ($post['acas'] as $aca) {
            try {
                $response = $this->dataManagement->update(entity: $this->modelClass, infos: [
                    'enviado' => 'Sim'
                ], id: $aca['id']);
            } catch (\Exception $ex) {

            }

            return $response;
        }
    }
}
