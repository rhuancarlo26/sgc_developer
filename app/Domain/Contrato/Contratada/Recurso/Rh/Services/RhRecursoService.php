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

    private const RH_FILLABLE = [
        'id_contrato',
        'nome',
        'telefone',
        'cpf',
        'email',
        'profissao',
        'funcao',
        'ctf',
        'ctf_validade',
        'conselho_classe',
        'numero_registro',
        'status',
        'obs',
        'curriculum_latte',
    ];

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
        $rh = RecursoRh::create($this->filterRhFields($request));

        return [
            'rh' => $rh->id,
            'request' => [
                'type' => 'success',
                'content' => 'RH cadastrado com sucesso!',
            ],
        ];
    }

    public function updateRh($request): array
    {
        
        $rh = RecursoRh::findOrFail($request['id']);

        $rh->update($this->filterRhFields($request));

        return [
            'request' => [
                'type' => 'success',
                'content' => 'RH atualizado com sucesso!',
            ],
        ];
    }

    private function filterRhFields(array $request): array
    {
        $data = array_intersect_key($request, array_flip(self::RH_FILLABLE));

        if (array_key_exists('status', $request)) {
            $data['status'] = (int) $request['status'];
        }

        if (array_key_exists('conselho_classe', $request)) {
            $data['conselho_classe'] = (int) $request['conselho_classe'];
        }

        if (($data['conselho_classe'] ?? null) === 0) {
            $data['numero_registro'] = null;
        }

        return $data;
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

    public function salvarCurriculum($request): array
    {
        $nome = $request['curriculum_pdf']->getClientOriginalName();
        $caminho = $request['curriculum_pdf']->storeAs('Contrato' . DIRECTORY_SEPARATOR . 'Recurso' . DIRECTORY_SEPARATOR . 'Rh' . DIRECTORY_SEPARATOR . uniqid() . '__' . $nome);

        return $this->dataManagement->update(entity: $this->modelClass, infos: [
            'curriculum_pdf' => $caminho
        ], id: $request['id']);
    }

    public function salvarFotoPerfil($request): array
    {
        $nome = $request['foto_perfil']->getClientOriginalName();
        $caminho = $request['foto_perfil']->storeAs('public' . DIRECTORY_SEPARATOR . 'Contrato' . DIRECTORY_SEPARATOR . 'Recurso' . DIRECTORY_SEPARATOR . 'Rh' . DIRECTORY_SEPARATOR . uniqid() . '__' . $nome);

        return $this->dataManagement->update(entity: $this->modelClass, infos: [
            'foto_perfil' => str_replace("public\\", "", $caminho)
        ], id: $request['id']);
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

    public function destroyCurriculum($rh): array
    {
        try {
            Storage::delete($rh->curriculum_pdf);

            return $this->dataManagement->update(entity: $this->modelClass, infos: [
                'curriculum_pdf' => null
            ], id: $rh->id);
        } catch (\Exception $th) {
            return [
                'request' => [
                    'type' => 'error',
                    'content' => $th->getMessage()
                ]
            ];
        }
    }

    public function destroyFotoPerfil($rh): array
    {
        try {
            Storage::delete($rh->foto_perfil);

            return $this->dataManagement->update(entity: $this->modelClass, infos: [
                'foto_perfil' => null
            ], id: $rh->id);
        } catch (\Exception $th) {
            return [
                'request' => [
                    'type' => 'error',
                    'content' => $th->getMessage()
                ]
            ];
        }
    }
}
