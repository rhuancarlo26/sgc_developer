<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Services;

use App\Models\RecursoRh;
use App\Models\RecursoRhDocumento;
use App\Models\RecursoRhDocumentoBaixa;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class RhRecursoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = RecursoRh::class;
    protected string $modelClassDocumento = RecursoRhDocumento::class;
    protected string $modelClassDocumentoBaixa = RecursoRhDocumentoBaixa::class;

    public function listagemRh($contrato, $searchParams)
    {
        return [
            'rhs' => $this->search(...$searchParams)
                ->with(['documentos'])
                ->where('id_contrato', $contrato->id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function salvarRh($request): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

        return [
            'rh' => $response['model']['id'],
            'request' => $response['request']
        ];
    }

    public function updateRh($request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

        return ['request' => $response['request']];
    }

    public function salvarDocumentoRh($request): array
    {
        $return = [];
        foreach ($request['documentos'] as $key => $value) {
            if ($value->isvalid()) {
                $request['nome_arquivo'] = $value->getClientOriginalName();
//                $request['tipo'] = $value->extension();
                $request['arquivo'] = $value->storeAs('Contrato' . DIRECTORY_SEPARATOR . 'Recurso' . DIRECTORY_SEPARATOR . 'Rh' . DIRECTORY_SEPARATOR . uniqid() . '_' . $key . '_' . $request['nome_arquivo']);

//                unset($request['documentos_baixa']);

                $response = $this->dataManagement->create(entity: $this->modelClassDocumento, infos: $request);

                $return = [
                    'request' => $response['request']
                ];
            }
        }
        return $return;
    }

    public function destroyRh($rh): array
    {
        try {
            $documentos = $this->modelClassDocumento::Where('cod_rh', $rh->id)->get();

            foreach ($documentos as $value) {
                Storage::delete($value->arquivo);
            }

            $this->delete($rh);

            return [
                'type' => 'success',
                'content' => "{$rh->nome} excluído com sucesso!",
            ];
        } catch (\Exception $th) {
            return ['type' => 'error', 'content' => $th->getMessage()];
        }
    }

    public function salvarDocumentoBaixaRh($request): array
    {
        foreach ($request['documentos_baixa'] as $key => $value) {
            if ($value->isvalid()) {
                $request['nome'] = $value->getClientOriginalName();
                $request['tipo'] = $value->extension();
                $request['caminho'] = $value->storeAs('Contrato' . DIRECTORY_SEPARATOR . 'Recurso' . DIRECTORY_SEPARATOR . 'Rh' . DIRECTORY_SEPARATOR . uniqid() . '_' . $key . '_' . $request['nome']);

                unset($request['documentos']);

                $response = $this->dataManagement->create(entity: $this->modelClassDocumentoBaixa, infos: $request);

                return [
                    'rh' => $response['model']['id'],
                    'request' => $response['request']
                ];
            }
        }
    }

    public function destroyDocumento($model_documento): array
    {
        try {
            Storage::delete($model_documento->arquivo);

            $this->delete($model_documento);

            return [
                'type' => 'success',
                'content' => "{$model_documento->nome_arquivo} excluído com sucesso!",
            ];
        } catch (\Exception $th) {
            return ['type' => 'error', 'content' => $th->getMessage()];
        }
    }
}
