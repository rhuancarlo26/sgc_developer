<?php

namespace App\Domain\Contrato\Contratada\Recurso\Equipamento\Services;

use App\Models\Contrato;
use App\Models\RecursoEquipamento;
use App\Models\RecursoEquipamentoDocumento;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class EquipamentoRecursoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = RecursoEquipamento::class;
    protected string $modelClassDocumento = RecursoEquipamentoDocumento::class;

    public function ListagemEquipamentos($contrato, $searchParams): array
    {
        return [
            'equipamentos' => $this->search(...$searchParams)
                ->with(['documentos'])
                ->where('id_contrato', $contrato->id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function salvarEquipamento($request): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

        return [
            'equipamento' => $response['model'],
            'request' => $response['request']
        ];
    }

    public function updateEquipamento(array $request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

        return [
            'request' => $response['request']
        ];
    }

    public function salvarDocumentoEquipamento($request): array
    {
        try {
            foreach ($request['documentos'] as $key => $value) {
                if ($value->isvalid()) {
                    $nome = $value->getClientOriginalName();
                    $tipo = $value->extension();
                    $caminho = $value->storeAs('Contrato' . DIRECTORY_SEPARATOR . 'Recurso' . DIRECTORY_SEPARATOR . 'Equipamento' . DIRECTORY_SEPARATOR . uniqid() . '_' . $key . '_' . $nome);

                    $this->modelClassDocumento::create([
                        'cod_equipamento' => $request['cod_equipamento'],
                        'nome_arquivo' => $nome,
                        'tipo' => $tipo,
                        'arquivo' => $caminho
                    ]);
                }
            }

            return ['type' => 'success', 'content' => 'Documentos cadastrados com sucesso!'];
        } catch (\Exception $th) {
            return ['type' => 'error', 'content' => $th->getMessage()];
        }
    }

    public function destroyEquipamento($equipamento): array
    {
        try {
            $documentos = $this->modelClassDocumento::Where('cod_equipamento', $equipamento->id)->get();

            foreach ($documentos as $value) {
                Storage::delete($value->arquivo);
            }
            return ['type' => 'success', 'content' => 'Documentos excluídos com sucesso!'];
        } catch (\Exception $th) {
            return ['type' => 'error', 'content' => $th->getMessage()];
        }
    }
}
