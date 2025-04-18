<?php

namespace App\Domain\Servico\PMQA\Execucao\Coleta\app\Services;

use App\Models\ServicoPmqaCampanhaPontoColeta;
use App\Models\ServicoPmqaCampanhaPontoColetaArquivo;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class ColetaService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPmqaCampanhaPontoColeta::class;
    protected string $modelClassArquivo = ServicoPmqaCampanhaPontoColetaArquivo::class;

    public function store(array $request): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function update(array $request): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
    }

    public function storeArquivo(array $request): array
    {
        $return = [];
        if ($request['arquivo']->isvalid()) {
            $nome = $request['arquivo']->getClientOriginalName();
            $tipo = $request['arquivo']->extension();
            $caminho = $request['arquivo']->storeAs('Servico' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Arquivo' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

            $return = $this->dataManagement->create(entity: $this->modelClassArquivo, infos: [
                'fk_ponto_coleta' => $request['id'],
                'nome' => $nome,
                'caminho_imagem' => $caminho
            ]);
        }
        return $return;
    }

    public function deleteArquivo(ServicoPmqaCampanhaPontoColetaArquivo $arquivo): array
    {
//        Storage::delete($arquivo->caminho);

        return $this->dataManagement->delete(entity: $this->modelClassArquivo, id: $arquivo->id);
    }
}
