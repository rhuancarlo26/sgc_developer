<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Services;

use App\Models\RecursoVeiculo;
use App\Models\RecursoVeiculoCodigo;
use App\Models\RecursoVeiculoDocumento;
use App\Models\RecursoVeiculoQuilometragem;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class VeiculoRecursoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = RecursoVeiculo::class;
    protected string $modelClassDocumento = RecursoVeiculoDocumento::class;
    protected string $modelClassQuilometragem = RecursoVeiculoQuilometragem::class;

    public function listagemVeiculos($contrato, $searchParams)
    {
        return [
            'veiculos' => $this->search(...$searchParams)
                ->with(['codigo', 'documentos'])
                ->where('id_contrato', $contrato->id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function createForm()
    {
        return [
            'codigos' => RecursoVeiculoCodigo::all()
        ];
    }

    public function salvarVeiculo(array $request): array
    {
        $request['cod_veiculos'] = $request['cod_veiculos']['id'];
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);
        return [
            'veiculo' => $response['model'],
            'request' => $response['request']
        ];
    }

    public function updateVeiculo(array $request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

        return [
            'request' => $response['request']
        ];
    }

    public function updateQuilometragemVeiculo(array $request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClassQuilometragem, infos: $request, id: $request['id']);

        return [
            'request' => $response['request']
        ];
    }

    public function salvarDocumentoVeiculo($request): array
    {
        try {
            foreach ($request['documentos'] as $key => $value) {
                if ($value->isvalid()) {
                    $nome = $value->getClientOriginalName();
                    $tipo = $value->extension();
                    $caminho = $value->storeAs('Contrato' . DIRECTORY_SEPARATOR . 'Recurso' . DIRECTORY_SEPARATOR . 'Veiculo' . DIRECTORY_SEPARATOR . uniqid() . '_' . $key . '_' . $nome);

                    $this->modelClassDocumento::create([
                        'cod_veiculo' => $request['cod_veiculo'],
                        'nome_arquivo' => $nome,
                        'tipo_arquivo' => $tipo,
                        'arquivo' => $caminho
                    ]);
                }
            }

            return ['type' => 'success', 'content' => 'Documentos cadastrados com sucesso!'];
        } catch (\Exception $th) {
            return ['type' => 'error', 'content' => $th->getMessage()];
        }
    }

    public function salvarQuilometragemVeiculo($request)
    {
        $response = $this->dataManagement->create(entity: $this->modelClassQuilometragem, infos: $request);

        return ['request' => $response['request']];
    }

    public function destroyVeiculo($veiculo)
    {
        try {
            $documentos = $this->modelClassDocumento::Where('cod_veiculo', $veiculo->id)->get();

            foreach ($documentos as $value) {
                Storage::delete($value->caminho);
            }
            return ['type' => 'success', 'content' => 'Documentos excluídos com sucesso!'];
        } catch (\Exception $th) {
            return ['type' => 'error', 'content' => $th->getMessage()];
        }
    }
}
