<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Anexo\Services;

use App\Models\ContratoAnexo;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class AnexoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ContratoAnexo::class;

    public function salvarAnexo($request)
    {
        $arquivo = $this->storageAnexo($request['arquivo']);

        $form = [
            ...$arquivo,
            'contrato_id' => $request['contrato_id'],
            'nome'        => $request['nome']
        ];

        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $form);

        return [
            'request' => $response['request']
        ];
    }

    public function update($request)
    {
        try {
            Storage::delete($request['caminho']);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $arquivo = $this->storageAnexo($request['arquivo']);

        $form = [
            ...$arquivo,
            'contrato_id' => $request['contrato_id'],
            'versao'      => $request['versao'] + 1,
            'nome'        => $request['nome']
        ];

        $introducao = $this->dataManagement->update(entity: $this->modelClass, infos: $form, id: $request['id']);

        return [
            'request' => $introducao['request']
        ];
    }

    public function storageAnexo($arquivo)
    {
        $nome = $arquivo->getClientOriginalName();
        $tipo = $arquivo->extension();
        $caminho = $arquivo->storeAs('Contrato' . DIRECTORY_SEPARATOR . 'Anexo' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

        return [
            'nome_arquivo' => $nome,
            'extencao' => $tipo,
            'caminho_arquivo' => $caminho
        ];
    }
}
